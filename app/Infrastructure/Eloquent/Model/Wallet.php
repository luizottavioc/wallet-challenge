<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property string $user_id
 * @property int $amount
 * @property Carbon $processed_at
 * @property User $user
 */
class Wallet extends Model
{
    public bool $incrementing = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wallets';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id',
        'user_id',
        'amount',
        'processed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'amount' => 'integer',
        'processed_at' => 'datetime'
    ];

    protected string $keyType = 'string';

    protected ?string $dateFormat = 'Y-m-d H:i:s.u';

    public bool $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
