<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_sets', function (Blueprint $table) {
            $table->id();
            $table->enum('game', ['pokemon', 'mtg', 'yugioh', 'one_piece', 'lorcana', 'fab', 'digimon', 'union_arena', 'other']);
            $table->string('name');
            $table->string('set_code', 20)->nullable();
            $table->date('release_date')->nullable();
            $table->integer('total_cards')->nullable();
            $table->string('series')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['game', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_sets');
    }
};
