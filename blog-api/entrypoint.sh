#!/bin/ash

echo "DB migration"
php bin/console doctrine:migrations:migrate   

exec "$@"
