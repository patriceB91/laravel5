#!/bin/bash
# To be run on the synology. Adjust vars below.
# Purpose is to reset all laravle file to what it should
# Copy this file one level above and allow +x
LARAVEL_PATH="/Volume2/web/kiosksadmin"
USER="patrice"
GROUP="http"

sudo chown -R {$USER}:{$GROUP} {$LARAVEL_PATH}

sudo find {$LARAVEL_PATH} -type f -exec chmod 644 {} \;

sudo find {$LARAVEL_PATH} -type d -exec chmod 755 {} \;

sudo chgrp -R {$USER} storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
# Allow file upload
sudo chmod 775 {$LARAVEL_PATH}/public/kiosks