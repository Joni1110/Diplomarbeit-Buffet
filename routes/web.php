<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Helper: Zugriffsschutz
|--------------------------------------------------------------------------
*/
function requireLogin()
{
    if (!Session::has('user_id')) {
        return redirect('/login')->with('error', 'Bitte zuerst einloggen.');
    }
    return null;
}

/*
|--------------------------------------------------------------------------
| Seiten
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/login'));

Route::get('/login', function () {
    if (Session::has('user_id')) return redirect('/home');
    return view('login');
});

/*
|--------------------------------------------------------------------------
| REGISTRIERUNG (GET) – Captcha erzeugen
|--------------------------------------------------------------------------
*/
Route::get('/register', function () {
    if (Session::has('user_id')) return redirect('/home');

    $a = random_int(1, 9);
    $b = random_int(1, 9);

    Session::put('captcha_answer', $a + $b);
    Session::put('captcha_question', "$a + $b");

    return view('register');
});

/*
|--------------------------------------------------------------------------
| VERIFY (GET) – Formular anzeigen (Code + Mail kommen per Link)
|--------------------------------------------------------------------------
*/
Route::get('/verify', fn () => view('verify'));

/*
|--------------------------------------------------------------------------
| VERIFY (POST) – E-Mail bestätigen (Route-Name verify.user)
|--------------------------------------------------------------------------
*/
Route::post('/verify', function (Request $request) {

    $request->validate([
        'email' => 'required|email',
        'code' => 'required|digits:6',
    ], [
        'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
        'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        'code.required' => 'Bestätigungscode fehlt.',
        'code.digits' => 'Der Bestätigungscode muss 6-stellig sein.',
    ]);

    $user = DB::table('benutzer')
        ->where('email', $request->email)
        ->where('verifizierungs_code', $request->code)
        ->first();

    if (!$user) {
        return back()->with('error', 'Link ungültig oder abgelaufen.');
    }

    DB::table('benutzer')->where('id', $user->id)->update([
        'email_verifiziert' => 1,
        'verifizierungs_code' => null
    ]);

    // ✅ Direkt einloggen
    Session::put('user_id', $user->id);
    Session::put('user_name', $user->vorname);
    Session::put('user_email', $user->email);
    Session::put('user_rolle', $user->rolle);

    return redirect('/home')->with('success', 'E-Mail erfolgreich bestätigt!');
})->name('verify.user');

/*
|--------------------------------------------------------------------------
| GESCHÜTZTE SEITEN
|--------------------------------------------------------------------------
*/
Route::get('/home', function () {
    if ($r = requireLogin()) return $r;
    return view('home');
});

Route::get('/bestellungen', function () {
    if ($r = requireLogin()) return $r;
    return view('bestellungen');
});

Route::get('/kontakt', function () {
    if ($r = requireLogin()) return $r;
    return view('kontakt');
});

Route::get('/ueberuns', function () {
    if ($r = requireLogin()) return $r;
    return view('ueberuns');
});

/*
|--------------------------------------------------------------------------
| PASSWORT VERGESSEN (ÖFFENTLICH)
|--------------------------------------------------------------------------
*/
Route::get('/passwort_vergessen', fn () => view('passwort_vergessen'));

Route::post('/passwort_vergessen', function (Request $request) {

    $request->validate([
        'email' => 'required|email'
    ], [
        'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
        'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
    ]);

    $user = DB::table('benutzer')->where('email', $request->email)->first();

    if (!$user) {
        return back()->with('error', 'E-Mail-Adresse nicht gefunden.');
    }

    $code = bin2hex(random_bytes(32));

    DB::table('benutzer')->where('id', $user->id)->update([
        'passwort_reset_code' => $code
    ]);

    $resetUrl = url('/passwort_reset') .
        '?email=' . urlencode($user->email) .
        '&code=' . $code;

    Mail::raw(
        "Hallo!\n\nKlicke auf folgenden Link, um dein Passwort zurückzusetzen:\n\n$resetUrl\n\nFalls du das nicht warst, ignoriere diese E-Mail.",
        function ($message) use ($user) {
            $message->to($user->email)
                ->subject('SmartBuffet – Passwort zurücksetzen');
        }
    );

    return back()->with('success', 'E-Mail zum Zurücksetzen wurde gesendet.');
})->name('passwort_vergessen');

/*
|--------------------------------------------------------------------------
| PASSWORT RESET
|--------------------------------------------------------------------------
*/
Route::get('/passwort_reset', fn () => view('reset_password'));

Route::post('/passwort_reset', function (Request $request) {

    $request->validate([
        'email' => 'required|email',
        'code' => 'required',
        'password' => 'required|confirmed|min:6',
    ], [
        'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
        'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        'code.required' => 'Der Reset-Link ist ungültig oder unvollständig.',
        'password.required' => 'Bitte gib ein neues Passwort ein.',
        'password.min' => 'Das Passwort muss mindestens 6 Zeichen lang sein.',
        'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
    ]);

    $user = DB::table('benutzer')
        ->where('email', $request->email)
        ->where('passwort_reset_code', $request->code)
        ->first();

    if (!$user) {
        return back()->with('error', 'Ungültiger oder abgelaufener Link.');
    }

    DB::table('benutzer')->where('id', $user->id)->update([
        'passwort' => Hash::make($request->password),
        'passwort_reset_code' => null
    ]);

    return redirect('/login')->with('success', 'Passwort erfolgreich geändert.');
})->name('password.reset');

/*
|--------------------------------------------------------------------------
| REGISTRIERUNG (POST) – Bot-Schutz + Rate-Limit
|--------------------------------------------------------------------------
*/
Route::post('/register', function (Request $request) {

    // Honeypot: Bots füllen das Feld aus
    if ($request->filled('website')) {
        return back()->with('error', 'Bot erkannt.')->withInput();
    }

    // Captcha prüfen
    $expected = Session::get('captcha_answer');
    if ((int)$request->input('captcha') !== (int)$expected) {
        return back()->with('error', 'Rechnung falsch – bitte probiere es erneut.')->withInput();
    }

    Session::forget('captcha_answer');
    Session::forget('captcha_question');

    $request->validate([
        'vorname' => 'required|string|max:50',
        'nachname' => 'required|string|max:50',
        'email' => [
            'required',
            'email',
            'unique:benutzer,email',
            'regex:/^[a-zA-Z0-9._%+-]+@hakneumarkt\.at$/'
        ],
        'password' => 'required|confirmed|min:6',
    ], [
        'vorname.required' => 'Bitte Vorname eingeben.',
        'nachname.required' => 'Bitte Nachname eingeben.',
        'email.required' => 'Bitte E-Mail eingeben.',
        'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        'email.unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
        'email.regex' => 'Bitte registriere dich mit deiner Schul-E-Mail-Adresse (@hakneumarkt.at).',
        'password.required' => 'Bitte Passwort eingeben.',
        'password.min' => 'Das Passwort muss mindestens 6 Zeichen lang sein.',
        'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
    ]);

    $code = random_int(100000, 999999);

    DB::table('benutzer')->insert([
        'vorname' => $request->vorname,
        'nachname' => $request->nachname,
        'email' => $request->email,
        'passwort' => Hash::make($request->password),
        'rolle' => 'kunde',
        'email_verifiziert' => 0,
        'verifizierungs_code' => (string)$code,
        'erstellt_am' => now(),
    ]);

    $verifyUrl = url('/verify') . '?email=' . urlencode($request->email) . '&code=' . $code;

    Mail::raw(
        "Bitte bestätige deine E-Mail:\n\n$verifyUrl",
        function ($message) use ($request) {
            $message->to($request->email)
                ->subject('SmartBuffet – E-Mail bestätigen');
        }
    );

    return redirect('/verify')->with('success', 'Bestätigungslink wurde gesendet.');
})->middleware('throttle:10,1')->name('register.user');

/*
|--------------------------------------------------------------------------
| LOGIN / LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/login', function (Request $request) {

    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ], [
        'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
        'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        'password.required' => 'Bitte gib dein Passwort ein.',
    ]);

    $user = DB::table('benutzer')->where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->passwort)) {
        return back()->with('error', 'Login fehlgeschlagen.');
    }

    if ((int)$user->email_verifiziert === 0) {
        return back()->with('error', 'Bitte bestätige zuerst deine E-Mail.');
    }

    Session::put('user_id', $user->id);
    Session::put('user_name', $user->vorname);
    Session::put('user_email', $user->email);
    Session::put('user_rolle', $user->rolle);

    return redirect('/home');
})->name('login.user');

Route::get('/logout', function () {
    Session::flush();
    return redirect('/login');
});
