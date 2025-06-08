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
        Schema::create('materiels', function (Blueprint $table) {
            $table->id();
            $table->string('nom_materiel');
            $table->text('Description');
            $table->string('matricule')->nullable();
            $table->integer('quantite_totale')->nullable();
            $table->integer('quantite_disponible')->nullable();
            $table->boolean('gestion_par_unite')->default(false);
            $table->enum('etat',['disponible','utilisé']);
            $table->unsignedBigInteger('categorie_materiel_id');
            $table->foreign('categorie_materiel_id')->references('id')->on('categorie_materiels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiels');
    }
};
