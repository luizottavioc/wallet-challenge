<?php

use App\Domain\Enum\TransactionStatusEnum;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payer_id');
            $table->foreign('payer_id')->references('id')->on('users');
            $table->unsignedBigInteger('payee_id')->index();
            $table->foreign('payee_id')->references('id')->on('users');
            $table->unsignedBigInteger('amount');
            $table->string('status')->default(TransactionStatusEnum::PENDING->value);
            $table->timestamp('created_at');
            $table->timestamp('processed_at')->nullable();

            $table->index(['payer_id', 'processed_at']);
            $table->index(['payee_id', 'processed_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
