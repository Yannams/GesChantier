<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chantier_materiel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chantier_id');
            $table->foreign('chantier_id')->references('id')->on('chantiers')->onDelete('cascade'); 
            $table->unsignedBigInteger('materiel_id');
            $table->foreign('materiel_id')->references('id')->on('materiels')->onDelete('cascade');
            $table->date('date_debut_affectation');
            $table->date('date_fin_affectation_prevue');
            $table->date('date_retour_effectif')->nullable();
            $table->text('remarques')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chantier_materiel');
    }
};
