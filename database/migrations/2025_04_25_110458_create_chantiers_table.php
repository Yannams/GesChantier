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
        Schema::create('chantiers', function (Blueprint $table) {
            $table->id();
            $table->string('nom_chantier');
            $table->string('localisation');
            $table->date('DateDebutPrevue')->nullable();
            $table->date('DateFinPrevue')->nullable();
            $table->date('DateDebutReelle')->nullable();
            $table->date('DateFinReelle')->nullable();
            $table->enum('Etat',['en attente','en cours', 'terminé']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chantiers');
    }
};
