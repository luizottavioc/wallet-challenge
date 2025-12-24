<?php

declare(strict_types=1);

use App\Domain\Enum\UserTypeEnum;
use App\Infrastructure\Model\User;
use Faker\Factory;
use Hyperf\Database\Seeders\Seeder;

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

        User::create([
            'name' => $faker->name(),
            'email' => $faker->email(),
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'cpf' => $faker->cpf(false),
            'cnpj' => null,
            'type' => UserTypeEnum::DEFAULT->value,
        ]);
    }
}
