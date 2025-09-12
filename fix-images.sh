#!/bin/bash

# Navigate to partners folder and replace partner images
cd /home3/vminingc/swaeduae.ae/laravel-app/public/partners || exit
rm -f partner*.png
wget -q -O partner1.png https://via.placeholder.com/150x60?text=Partner+1
wget -q -O partner2.png https://via.placeholder.com/150x60?text=Partner+2
wget -q -O partner3.png https://via.placeholder.com/150x60?text=Partner+3
wget -q -O partner4.png https://via.placeholder.com/150x60?text=Partner+4
wget -q -O partner5.png https://via.placeholder.com/150x60?text=Partner+5

# Navigate to images folder and replace step and appstore images
cd ../images || exit
rm -f step*.png appstore.png
wget -q -O step1.png https://via.placeholder.com/56?text=Step+1
wget -q -O step2.png https://via.placeholder.com/56?text=Step+2
wget -q -O step3.png https://via.placeholder.com/56?text=Step+3
wget -q -O appstore.png https://via.placeholder.com/200x60?text=App+Store

echo "Placeholder images updated successfully."
