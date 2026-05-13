<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->string('player_name');
            $table->integer('score');
            $table->integer('attempts_used');
            $table->boolean('won');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('game_scores'); }
};
