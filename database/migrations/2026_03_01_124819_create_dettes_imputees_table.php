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
        Schema::create('dettes_imputees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colocation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membre_retire_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payeur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('beneficiaire_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('montant', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dettes_imputees');
    }
};
