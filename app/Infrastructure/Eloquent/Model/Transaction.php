<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property string $payer_id
 * @property string $payee_id
 * @property int $amount
 * @property Carbon $processed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property User $payer
 * @property User $payee
 */
class Transaction extends Model
{
    use SoftDeletes;

    public bool $incrementing = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id',
        'payer_id',
        'payee_id',
        'amount',
        'processed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'string',
        'payer_id' => 'string',
        'payee_id' => 'string',
        'amount' => 'integer',
        'processed_at' => 'datetime'
    ];

    protected string $keyType = 'string';

    protected ?string $dateFormat = 'Y-m-d H:i:s.u';

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id', 'id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id', 'id');
    }
}
