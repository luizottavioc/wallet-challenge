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
            $table->timestamp('processed_at', 6)->nullable();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index(['payer_id', 'processed_at', 'deleted_at']);
            $table->index(['payee_id', 'processed_at', 'deleted_at']);
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
