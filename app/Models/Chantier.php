<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chantier extends Model
{
    /** @use HasFactory<\Database\Factories\ChantierFactory> */
    use HasFactory;

    protected $fillable = ['nom_chantier','localisation','DateDebutPrevue','DateFinPrevue','DateDebutReelle','DateFinReelle','Etat','created_at','updated_at'];

    public function materiels() :BelongsToMany
     {
        return $this->belongsToMany(Materiel::class)->withPivot('date_debut_affectation','date_fin_affectation','created_at','updated_at');
     }
}
