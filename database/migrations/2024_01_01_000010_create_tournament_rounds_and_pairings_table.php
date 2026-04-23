<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->cascadeOnDelete();
            $table->integer('round_number');
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->boolean('is_top_cut')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['tournament_id', 'round_number']);
            $table->index(['tournament_id', 'status']);
        });

        Schema::create('pairings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_round_id')->constrained('tournament_rounds')->cascadeOnDelete();
            $table->integer('table_number');
            $table->foreignId('player1_registration_id')->constrained('tournament_registrations');
            $table->foreignId('player2_registration_id')->nullable()->constrained('tournament_registrations')->nullOnDelete();
            $table->integer('player1_games_won')->default(0);
            $table->integer('player2_games_won')->default(0);
            $table->integer('draws')->default(0);
            $table->enum('result', ['pending', 'player1_win', 'player2_win', 'draw', 'bye', 'double_loss'])->default('pending');
            $table->boolean('is_intentional_draw')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['tournament_round_id', 'result']);
        });

        Schema::create('prize_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->cascadeOnDelete();
            $table->foreignId('player_registration_id')->constrained('tournament_registrations');
            $table->integer('placement');
            $table->decimal('cash_amount', 8, 2)->default(0);
            $table->string('prize_description')->nullable();
            $table->boolean('paid_out')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prize_payouts');
        Schema::dropIfExists('pairings');
        Schema::dropIfExists('tournament_rounds');
    }
};
