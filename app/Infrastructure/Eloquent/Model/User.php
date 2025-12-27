<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $cpf
 * @property string $cnpj
 * @property string $password
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class User extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
