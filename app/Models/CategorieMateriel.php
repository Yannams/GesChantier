<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieMateriel extends Model
{
    /** @use HasFactory<\Database\Factories\CategorieMaterielFactory> */
    use HasFactory;

    protected $fillable =['nom_categorieMat','description'];

    public function materiels() :HasMany
    {
        return $this->hasMany(Materiel::class);
    }
}
