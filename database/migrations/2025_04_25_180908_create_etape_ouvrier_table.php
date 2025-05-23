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
        Schema::create('etape_ouvrier', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etape_id');
            $table->foreign('etape_id')->references('id')->on('etapes')->onDelete('cascade'); 
            $table->unsignedBigInteger('ouvrier_id');
            $table->foreign('ouvrier_id')->references('id')->on('ouvriers')->onDelete('cascade');
            $table->enum('EtatAffectation',['affecté','désaffecté']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etape_ouvrier');
    }
};
