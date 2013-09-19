sudo find /var/www/userdata -type f -mtime +10 -print | xargs -I {} rm {}
cp /var/www/mwms/at/.htaccess /var/www/userdata
