<?php

declare(strict_types=1);

use App\Domain\Enum\UserTypeEnum;
use App\Infrastructure\Eloquent\Model\User;
use Faker\Factory;
use Hyperf\Database\Seeders\Seeder;

class UserShopkeeperSeeder extends Seeder
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

        User::create([
            'name' => $faker->name(),
            'email' => $faker->email(),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'cpf' => null,
            'cnpj' => $faker->cnpj(false),
            'type' => UserTypeEnum::SHOPKEEPER->value,
        ]);

        echo "Shopkeeper user created:\n";
        echo "- Email: $email\n";
        echo "- Password: $password\n";
    }
}
