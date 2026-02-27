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
        Schema::create('adhesions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('colocation_id')->constrained('colocations')->cascadeOnDelete();
            $table->enum('role_dans_colocation', ['owner', 'membre']);
            $table->timestamp('date_adhesion')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            $table->unique(['utilisateur_id', 'colocation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adhesions');
    }
};
