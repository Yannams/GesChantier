<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chantier extends Model
{
    /** @use HasFactory<\Database\Factories\ChantierFactory> */
    use HasFactory;

    protected $fillable = ['nom_chantier','localisation','DateDebutPrevue','DateFinPrevue','DateDebutReelle','DateFinReelle','Etat','created_at','updated_at'];
}
