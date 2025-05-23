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
        Schema::create('etapes', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->text('Description');
            $table->date('DateDebutPrevue');
            $table->date('DateFinPrevue');
            $table->date('DateDebutReelle')->nullable();
            $table->date('DateFinReelle')->nullable();
            $table->enum('Etat', ['en attente','en cours','terminé'] );
            $table->unsignedBigInteger('tache_id');
            $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapes');
    }
};
