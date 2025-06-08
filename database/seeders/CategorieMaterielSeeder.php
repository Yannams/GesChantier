<?php

namespace Database\Seeders;

use App\Models\CategorieMateriel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorieMaterielSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategorieMateriel::create([
            'nom_categorieMat'=>'Engins de terrassement',
            'description'=>'Pour les travaux de sol (creuser, niveler, déplacer la terre)',
        ]);
         CategorieMateriel::create([
            'nom_categorieMat'=>'Engins de levage et de manutention',
            'description'=>'Pour soulever ou déplacer des charges lourdes ',
        ]);
         CategorieMateriel::create([
            'nom_categorieMat'=>'Matériel de construction',
            'description'=>'Pour la mise en œuvre des matériaux',
        ]);
    }
}
