# wallet-challenge

- docker-compose up -d
- php bin/hyperf.php migrate
- php bin/hyperf.php db:seed --path=seeders/user_default_seeder.php
- php bin/hyperf.php db:seed --path=seeders/user_shopkeeper_seeder.php