#!/usr/bin/env bash

if [ $# -lt 1 ]; then
    echo "usage: $0 <slug> [replace-text-domain] [dev-url]"
    exit 1
fi

SLUG=$1
REPLACE_TEXT_DOMAIN=${2-false}
DEV_URL=${3-${SLUG}.wordpress.dev}
FILE="./resources/assets/config-local.json"

echo "Creating config-local.json..."
cat > $FILE <<EOF
{
  "publicPath": "/wp-content/themes/${SLUG}",
  "devUrl": "http://${DEV_URL}"
}
EOF
