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
        Schema::create('tournament_teams', function (Blueprint $table) {
            $table->id();

            $table->integer('tournament_id');

            $table->string('name');

            $table->integer('vq3_player_id')->nullable();
            $table->integer('cpm_player_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_teams');
    }
};