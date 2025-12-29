<?php

declare(strict_types=1);

use App\Domain\Enum\UserTypeEnum;
use App\Infrastructure\Eloquent\Model\User;
use App\Infrastructure\Eloquent\Model\Wallet;
use Faker\Factory;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\Stringable\Str;
use function Hyperf\Support\now;

class UserDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('pt_BR');
        $email = $faker->email();
        $password = 'password';
        $walletAmount = 5000;

        $user = User::create([
            'id' => Str::uuid(),
            'name' => $faker->name(),
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'cpf' => $faker->cpf(false),
            'cnpj' => null,
            'type' => UserTypeEnum::DEFAULT->value,
        ]);

        Wallet::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'amount' => 5000,
            'processed_at' => now('UTC')->format('Y-m-d H:i:s.u'),
        ]);

        echo "Default user created:\n";
        echo "- Email: $email\n";
        echo "- Password: $password\n";
        echo "- Initial wallet amount: $ " . $walletAmount / 100 . "\n";
    }
}
