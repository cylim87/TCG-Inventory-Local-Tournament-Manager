<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->enum('category', [
                'booster_box', 'carton', 'booster_pack', 'single_card',
                'elite_trainer_box', 'starter_deck', 'bundle', 'accessory', 'supply', 'other'
            ]);
            $table->foreignId('card_set_id')->nullable()->constrained('card_sets')->nullOnDelete();
            $table->enum('game', ['pokemon', 'mtg', 'yugioh', 'one_piece', 'lorcana', 'fab', 'digimon', 'union_arena', 'other'])->nullable();
            $table->text('description')->nullable();
            $table->decimal('msrp', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->integer('boxes_per_carton')->default(6);
            $table->integer('packs_per_box')->nullable();
            $table->integer('cards_per_pack')->nullable();
            $table->string('barcode')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'game', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
