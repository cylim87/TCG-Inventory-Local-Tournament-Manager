<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('game', ['pokemon', 'mtg', 'yugioh', 'one_piece', 'lorcana', 'fab', 'digimon', 'union_arena', 'other']);
            $table->enum('format', [
                'standard', 'expanded', 'modern', 'legacy', 'pioneer', 'vintage',
                'draft', 'sealed', 'commander', 'pre_release', 'limited', 'other'
            ]);
            $table->date('date');
            $table->time('start_time')->default('18:00:00');
            $table->decimal('entry_fee', 8, 2)->default(0);
            $table->decimal('prize_pool', 8, 2)->default(0);
            $table->integer('max_players')->nullable();
            $table->integer('rounds')->nullable();
            $table->integer('top_cut')->default(0);
            $table->enum('status', ['registration', 'active', 'top_cut', 'completed', 'cancelled'])->default('registration');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->index(['status', 'date']);
            $table->index(['game', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
