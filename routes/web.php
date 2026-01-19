<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Helper: Zugriffsschutz + Auto-Logout wenn NICHT "remember"
|--------------------------------------------------------------------------
*/
function requireLogin()
{
    if (!Session::has('user_id')) {
        return redirect('/login')->with('error', 'Bitte zuerst einloggen.');
    }

    // Auto-Logout nach 120 Minuten, WENN NICHT "Angemeldet bleiben"
    $remember = Session::get('remember_me', false);

    if (!$remember) {
        $last = Session::get('last_activity');

        if ($last && (time() - $last) > (120 * 60)) { // 120 Minuten
            Session::flush();
            return redirect('/login')->with('error', 'Du wurdest automatisch ausgeloggt (Inaktivität).');
        }
    }

    // activity updaten
    Session::put('last_activity', time());

    return null;
}

/*
|--------------------------------------------------------------------------
| Seiten (öffentlich)
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/login'));

Route::get('/login', function () {
    if (Session::has('user_id')) return redirect('/home');
    return view('login');
})->name('login.form');

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
})->name('register.form');

/*
|--------------------------------------------------------------------------
| VERIFY (GET/POST)
|--------------------------------------------------------------------------
*/
Route::get('/verify', fn () => view('verify'))->name('verify.form');

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

    // Direkt einloggen
    Session::put('user_id', $user->id);
    Session::put('user_name', $user->vorname);
    Session::put('user_email', $user->email);
    Session::put('user_rolle', $user->rolle);

    Session::put('remember_me', false);
    Session::put('last_activity', time());

    return redirect('/home')->with('success', 'E-Mail erfolgreich bestätigt!');
})->name('verify.user');

/*
|--------------------------------------------------------------------------
| GESCHÜTZTE SEITEN
|--------------------------------------------------------------------------
*/
Route::get('/home', function () {
    if ($r = requireLogin()) return $r;

    // Produkte holen (wenn du nur aktive willst ->where('aktiv',1))
    $produkte = DB::table('produkte')
        ->orderBy('id', 'desc')
        ->get();

    return view('home', compact('produkte'));
})->name('home');

/*
|--------------------------------------------------------------------------
| WARENKORB (SESSION) + CHECKOUT
|--------------------------------------------------------------------------
| cart Struktur:
| Session::get('cart') = [ produkt_id => menge ]
*/

// Produkt in den Warenkorb
Route::post('/cart/add', function (Request $request) {
    if ($r = requireLogin()) return $r;

    $request->validate([
        'produkt_id' => 'required|integer',
        'menge' => 'required|integer|min:1|max:20',
    ], [
        'menge.min' => 'Mindestens 1 Stück.',
        'menge.max' => 'Maximal 20 Stück.',
    ]);

    $produkt = DB::table('produkte')->where('id', $request->produkt_id)->first();
    if (!$produkt) {
        return back()->with('error', 'Produkt nicht gefunden.');
    }

    $cart = Session::get('cart', []);
    $pid = (int)$request->produkt_id;
    $qty = (int)$request->menge;

    $cart[$pid] = ($cart[$pid] ?? 0) + $qty;

    // Sicherheit: max 20 pro Produkt
    if ($cart[$pid] > 20) $cart[$pid] = 20;

    Session::put('cart', $cart);

    return back()->with('success', 'Produkt wurde in den Warenkorb gelegt.');
})->name('cart.add');

// Menge ändern (optional, auf Bestellungen-Seite)
Route::post('/cart/update', function (Request $request) {
    if ($r = requireLogin()) return $r;

    $request->validate([
        'produkt_id' => 'required|integer',
        'menge' => 'required|integer|min:0|max:20',
    ]);

    $cart = Session::get('cart', []);
    $pid = (int)$request->produkt_id;
    $qty = (int)$request->menge;

    if ($qty <= 0) {
        unset($cart[$pid]);
    } else {
        $cart[$pid] = $qty;
    }

    Session::put('cart', $cart);

    return back()->with('success', 'Warenkorb aktualisiert.');
})->name('cart.update');

// Warenkorb leeren
Route::post('/cart/clear', function () {
    if ($r = requireLogin()) return $r;

    Session::forget('cart');
    return back()->with('success', 'Warenkorb wurde geleert.');
})->name('cart.clear');

// Checkout: Bestellung in DB schreiben
Route::post('/checkout', function () {
    if ($r = requireLogin()) return $r;

    $cart = Session::get('cart', []);
    if (empty($cart)) {
        return back()->with('error', 'Warenkorb ist leer.');
    }

    $produktIds = array_keys($cart);

    $produkte = DB::table('produkte')
        ->whereIn('id', $produktIds)
        ->get()
        ->keyBy('id');

    // Falls Produkte gelöscht wurden
    foreach ($produktIds as $pid) {
        if (!isset($produkte[$pid])) {
            return back()->with('error', 'Ein Produkt im Warenkorb existiert nicht mehr.');
        }
    }

    // Gesamtpreis berechnen
    $gesamt = 0.0;
    foreach ($cart as $pid => $qty) {
        $preis = (float)$produkte[$pid]->preis;
        $gesamt += $preis * (int)$qty;
    }

    $userId = (int)Session::get('user_id');

    DB::beginTransaction();
    try {
        // 1) Bestellung anlegen
        $bestellungId = DB::table('bestellungen')->insertGetId([
            'benutzer_id' => $userId,
            'bestellt_am' => now(),
            'gesamtpreis' => $gesamt,
        ]);

        // 2) Positionen
        foreach ($cart as $pid => $qty) {
            DB::table('bestell_zwischen')->insert([
                'bestellung_id' => $bestellungId,
                'produkt_id' => (int)$pid,
                'menge' => (int)$qty,
                'einzelpreis' => (float)$produkte[$pid]->preis,
            ]);
        }

        DB::commit();

        // Warenkorb leeren
        Session::forget('cart');

        return redirect('/bestellungen')->with('success', 'Bestellung erfolgreich gespeichert!');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Fehler beim Speichern der Bestellung.');
    }
})->name('checkout');

/*
|--------------------------------------------------------------------------
| BESTELLUNGEN (Warenkorb + Historie)
|--------------------------------------------------------------------------
*/
Route::get('/bestellungen', function () {
    if ($r = requireLogin()) return $r;

    $userId = (int)Session::get('user_id');

    // Warenkorb laden + Produkte
    $cart = Session::get('cart', []);
    $cartItems = [];
    $cartTotal = 0.0;

    if (!empty($cart)) {
        $produktIds = array_keys($cart);
        $produkte = DB::table('produkte')->whereIn('id', $produktIds)->get()->keyBy('id');

        foreach ($cart as $pid => $qty) {
            if (!isset($produkte[$pid])) continue;

            $p = $produkte[$pid];
            $preis = (float)$p->preis;
            $summe = $preis * (int)$qty;

            $cartItems[] = (object)[
                'id' => (int)$p->id,
                'name' => $p->name,
                'preis' => $preis,
                'menge' => (int)$qty,
                'summe' => $summe,
            ];

            $cartTotal += $summe;
        }
    }

    // Bestellungen
    $bestellungen = DB::table('bestellungen')
        ->where('benutzer_id', $userId)
        ->orderBy('bestellt_am', 'desc')
        ->get();

    // Positionen zu Bestellungen
    $bestellungIds = $bestellungen->pluck('id')->all();
    $positionenByBestellung = [];

    if (!empty($bestellungIds)) {
        $positionen = DB::table('bestell_zwischen')
            ->join('produkte', 'produkte.id', '=', 'bestell_zwischen.produkt_id')
            ->whereIn('bestell_zwischen.bestellung_id', $bestellungIds)
            ->select(
                'bestell_zwischen.bestellung_id',
                'produkte.name as produkt_name',
                'bestell_zwischen.menge',
                'bestell_zwischen.einzelpreis'
            )
            ->orderBy('bestell_zwischen.bestellung_id', 'desc')
            ->get();

        foreach ($positionen as $pos) {
            $positionenByBestellung[$pos->bestellung_id][] = $pos;
        }
    }

    return view('bestellungen', [
        'cartItems' => $cartItems,
        'cartTotal' => $cartTotal,
        'bestellungen' => $bestellungen,
        'positionenByBestellung' => $positionenByBestellung
    ]);
})->name('bestellungen');

/*
|--------------------------------------------------------------------------
| KONTAKT (geschützt) + Mail senden
|--------------------------------------------------------------------------
*/
Route::get('/kontakt', function () {
    if ($r = requireLogin()) return $r;
    return view('kontakt');
})->name('kontakt');

Route::post('/kontakt', function (Request $request) {
    if ($r = requireLogin()) return $r;

    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:150',
        'nachricht' => 'required|string|min:10|max:2000',
    ], [
        'name.required' => 'Bitte gib deinen Namen ein.',
        'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
        'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        'nachricht.required' => 'Bitte gib eine Nachricht ein.',
        'nachricht.min' => 'Die Nachricht muss mindestens 10 Zeichen haben.',
        'nachricht.max' => 'Die Nachricht ist zu lang (max. 2000 Zeichen).',
    ]);

    $to = env('CONTACT_TO_ADDRESS', env('MAIL_FROM_ADDRESS'));
    $userInfo = Session::has('user_email') ? ' | User: ' . Session::get('user_email') : '';

    Mail::raw(
        "Neue Kontaktanfrage über SmartBuffet\n\n" .
        "Name: {$request->name}\n" .
        "E-Mail: {$request->email}\n" .
        "----------------------------------\n" .
        "{$request->nachricht}\n",
        function ($message) use ($request, $to, $userInfo) {
            $message->to($to)
                ->subject('SmartBuffet Kontaktformular' . $userInfo)
                ->replyTo($request->email, $request->name);
        }
    );

    return back()->with('success', 'Danke! Deine Nachricht wurde gesendet.');
})->middleware('throttle:5,1')->name('kontakt.send');

/*
|--------------------------------------------------------------------------
| ÜBER UNS (geschützt)
|--------------------------------------------------------------------------
*/
Route::get('/ueberuns', function () {
    if ($r = requireLogin()) return $r;
    return view('ueberuns');
})->name('ueberuns');

/*
|--------------------------------------------------------------------------
| PASSWORT VERGESSEN (ÖFFENTLICH)
|--------------------------------------------------------------------------
*/
Route::get('/passwort_vergessen', fn () => view('passwort_vergessen'))->name('passwort_vergessen.form');

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

    $resetUrl = url('/passwort_reset') . '?email=' . urlencode($user->email) . '&code=' . $code;

    Mail::raw(
        "Hallo!\n\nKlicke auf folgenden Link, um dein Passwort zurückzusetzen:\n\n$resetUrl\n\nFalls du das nicht warst, ignoriere diese E-Mail.",
        function ($message) use ($user) {
            $message->to($user->email)->subject('SmartBuffet – Passwort zurücksetzen');
        }
    );

    return back()->with('success', 'E-Mail zum Zurücksetzen wurde gesendet.');
})->name('passwort_vergessen');

Route::get('/passwort_reset', fn () => view('reset_password'))->name('passwort_reset.form');

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
| REGISTRIERUNG (POST)
|--------------------------------------------------------------------------
*/
Route::post('/register', function (Request $request) {

    // Honeypot
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
            $message->to($request->email)->subject('SmartBuffet – E-Mail bestätigen');
        }
    );

    return redirect('/verify')->with('success', 'Bestätigungslink wurde gesendet.');
})->middleware('throttle:10,1')->name('register.user');

/*
|--------------------------------------------------------------------------
| LOGIN / LOGOUT + "Angemeldet bleiben"
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

    // Checkbox aus Login-Form (falls du sie schon eingebaut hast)
    $remember = $request->filled('remember');
    Session::put('remember_me', $remember);
    Session::put('last_activity', time());

    return redirect('/home')->with('success', 'Erfolgreich eingeloggt!');
})->name('login.user');

Route::get('/logout', function () {
    Session::flush();
    return redirect('/login')->with('success', 'Du wurdest abgemeldet.');
})->name('logout');
