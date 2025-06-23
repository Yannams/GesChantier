<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materiel extends Model
{
    /** @use HasFactory<\Database\Factories\MaterielFactory> */
    use HasFactory;

    protected $fillable = ['matricule','nom_materiel', 'Description', 'etat', 'categorie_materiel_id','gestion_par_unite','quantite_disponible','quantite_totale'];

    public function categorieMateriel():BelongsTo
    {
        return $this->belongsTo(CategorieMateriel::class);
    }

    public function chantiers():BelongsToMany
    {
        return $this->belongsToMany(Chantier::class)->withPivot('id','date_debut_affectation','date_fin_affectation_prevue','date_retour_effectif','quantite','remarques','created_at','updated_at');
    }
}
