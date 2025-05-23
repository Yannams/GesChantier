<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueEtatEtape extends Model
{
    /** @use HasFactory<\Database\Factories\HistoriqueEtatEtapeFactory> */
    use HasFactory;

    protected $fillable=['etape_id','Etat','created_at','updated_at'];
}
