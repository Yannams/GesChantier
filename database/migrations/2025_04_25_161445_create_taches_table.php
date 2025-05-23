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
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->text('Description');
            $table->enum('statut',['programmé','en cours', 'terminé']);
            $table->unsignedBigInteger(column: 'chantier_id')->nullable();
            $table->foreign('chantier_id')->references('id')->on('chantiers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
