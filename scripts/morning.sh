#!/bin/sh

docker exec vs-system-backend_php sh -c "cd /webapp ; php artisan status:agendamento"
date
