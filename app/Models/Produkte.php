<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Produkte extends Model
{
    protected $table = 'produkte';
    protected $fillable = ['name', 'beschreibung', 'preis', 'bild_url', 'aktiv'];

}

