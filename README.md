Песочница для практики фишек php > 8.2 и api platform > 3.1

1`$ docker-compose up -d `

## Генерация ключей

0. `$ /var/www/symfony` (или локальная дериктория проекта)
1. `openssl genrsa -aes128 -passout pass:`~~OAUTH_PRIVATE_KEY_PASS~~ ` -out private.key 2048`
2. `openssl rsa -in private.key -passin pass:`~~OAUTH_PRIVATE_KEY_PASS~~` -pubout -out public.key`


## Запуск CS-fixer

vendor/bin/php-cs-fixer fix src

## Запуск PHP-stan

vendor/bin/phpstan analyse src tests
