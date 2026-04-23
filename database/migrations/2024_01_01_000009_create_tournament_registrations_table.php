<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players');
            $table->integer('seed')->nullable();
            $table->boolean('paid')->default(false);
            $table->boolean('dropped')->default(false);
            $table->integer('drop_round')->nullable();
            $table->string('deck_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tournament_id', 'player_id']);
            $table->index(['tournament_id', 'dropped']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_registrations');
    }
};
