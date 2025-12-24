<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property int $processed_at
 */
class Wallet extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wallets';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'user_id',
        'amount',
        'processed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'integer',
        'processed_at' => 'timestamp'
    ];

    public bool $timestamps = false;
}
