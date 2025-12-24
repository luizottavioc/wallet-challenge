<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $payer_id
 * @property int $payee_id
 * @property int $amount
 * @property string $status
 * @property int $created_at
 * @property int $processed_at
 */
class Transaction extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'payer_id',
        'payee_id',
        'amount',
        'status',
        'processed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'payer_id' => 'integer',
        'payee_id' => 'integer',
        'amount' => 'integer',
        'created_at' => 'timestamp',
        'processed_at' => 'timestamp'
    ];
}
