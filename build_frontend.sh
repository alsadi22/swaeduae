#!/bin/bash
cd /home3/vminingc/swaeduae.ae/laravel-app
if ! [ -x "$(command -v npm)" ]; then
  echo "Error: npm is not installed." >&2
  exit 1
fi
npm install
npm run build
