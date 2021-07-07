#!/bin/sh
service apache2 restart
service cron start
supervisord -c /etc/supervisor/supervisord.conf
supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:*
exec /sbin/init