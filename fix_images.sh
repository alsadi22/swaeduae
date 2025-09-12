#!/bin/bash

IMG_PATH="/home3/vminingc/swaeduae.ae/laravel-app/public/images"
PARTNERS_PATH="/home3/vminingc/swaeduae.ae/laravel-app/public/partners"
LARAVEL_PATH="/home3/vminingc/swaeduae.ae/laravel-app"

echo "Downloading images..."

curl -A "Mozilla/5.0" -o "$IMG_PATH/appstore.png" https://placehold.co/200x60?text=App+Store
curl -A "Mozilla/5.0" -o "$IMG_PATH/step1.png" https://placehold.co/56x56?text=Step+1
curl -A "Mozilla/5.0" -o "$IMG_PATH/step2.png" https://placehold.co/56x56?text=Step+2
curl -A "Mozilla/5.0" -o "$IMG_PATH/step3.png" https://placehold.co/56x56?text=Step+3

curl -A "Mozilla/5.0" -o "$PARTNERS_PATH/partner1.png" https://placehold.co/100x100?text=Partner+1
curl -A "Mozilla/5.0" -o "$PARTNERS_PATH/partner2.png" https://placehold.co/100x100?text=Partner+2
curl -A "Mozilla/5.0" -o "$PARTNERS_PATH/partner3.png" https://placehold.co/100x100?text=Partner+3
curl -A "Mozilla/5.0" -o "$PARTNERS_PATH/partner4.png" https://placehold.co/100x100?text=Partner+4
curl -A "Mozilla/5.0" -o "$PARTNERS_PATH/partner5.png" https://placehold.co/100x100?text=Partner+5

echo "Setting permissions..."

chmod 644 "$IMG_PATH"/*.png
chmod 644 "$PARTNERS_PATH"/*.png

echo "Clearing Laravel caches..."

cd "$LARAVEL_PATH"
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Done."
