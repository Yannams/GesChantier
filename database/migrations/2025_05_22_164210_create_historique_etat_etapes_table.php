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
        Schema::create('historique_etat_etapes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etape_id');
            $table->foreign('etape_id')->references('id')->on('etapes')->onDelete('cascade');
            $table->enum('Etat', ['en attente','en cours','terminé']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_etat_etapes');
    }
};
