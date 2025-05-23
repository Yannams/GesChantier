<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Etape extends Model
{
    /** @use HasFactory<\Database\Factories\EtapeFactory> */
    use HasFactory;

    protected $fillable=['libelle','Description','Etat','DateDebutPrevue','DateFinPrevue','DateDebutReelle','DateFinReelle','tache_id','created_at', 'updated_at'];

    public function ouvriers():BelongsToMany
    {
        return $this->belongsToMany(Ouvrier::class)->withPivot('EtatAffectation','created_at','updated_at');
    }


}
