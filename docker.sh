docker-compose up -d
docker-compose exec php /bin/sh -c "composer self-update; cd /var/www/src/Ramble && /bin/sh build.sh unattended"
