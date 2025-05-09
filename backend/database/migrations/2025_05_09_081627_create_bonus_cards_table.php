<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bonus_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('card_number')->unique();
            $table->integer('current_level')->default(1);
            $table->integer('max_level');
            $table->integer('points')->default(0);
            $table->integer('points_to_next_level');
            $table->timestamps();
            
            $table->index('card_number');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bonus_cards');
    }
};