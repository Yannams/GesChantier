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
        Schema::create('etape_equipe', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etape_id');
            $table->foreign('etape_id')->references('id')->on('etapes')->onDelete('cascade'); 
            $table->unsignedBigInteger('equipe_id');
            $table->foreign('equipe_id')->references('id')->on('equipes')->onDelete('cascade');
            $table->date('DateDebutReelle');
            $table->date('DateFinReelle');
            $table->text('Rapport');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etape_equipe');
    }
};
