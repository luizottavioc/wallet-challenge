<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('cpf')->unique();
            $table->string('cnpj')->unique();
            $table->string('password');
            $table->string('type')->default(''); // todo: enum
            $table->datetimes();
            $table->softDeletes();

            $table->index('email');
            $table->index('cpf');
            $table->index('cnpj');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
