<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ouvrier extends Model
{
    /** @use HasFactory<\Database\Factories\OuvrierFactory> */
    use HasFactory;

    protected $fillable=['nom', 'Prenom', 'specialisation', 'Contact', 'Adresse','created_at', 'updated_at'];

    public function etapes(): BelongsToMany
    {
        return $this->belongsToMany(Etape::class)->withPivot( 'EtatAffectation','created_at','updated_at');
    }
}
