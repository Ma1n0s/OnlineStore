<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('patronymic')->nullable();
            
            $table->string('company_name')->nullable();
            $table->string('inn')->nullable();
            $table->string('kpp')->nullable();
            $table->string('phone')->nullable(); 
            $table->string('email')->nullable(); 
            $table->text('legal_address')->nullable();
            $table->string('director')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};