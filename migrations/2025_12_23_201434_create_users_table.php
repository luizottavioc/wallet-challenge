<?php

use App\Domain\Enum\UserTypeEnum;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('type')->default(UserTypeEnum::DEFAULT->value);
            $table->string('cpf')->nullable()->unique();
            $table->string('cnpj')->nullable()->unique();
            $table->string('password');
            $table->timestamps();
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
