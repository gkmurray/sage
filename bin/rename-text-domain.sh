#!/usr/bin/env bash

if [ $# -lt 1 ]; then
    echo "usage: $0 <new-slug> [replace-text-domain]"
    exit 1
fi

DEFAULT_SLUG="sage"
DEFAULT_PUBLIC_PATH="/app/themes/sage"
DEFAULT_DEV_URL="http://example.dev"

SLUG=$1
REPLACE_TEXT_DOMAIN=${2-false}

PUBLIC_PATH="/wp-content/themes/${SLUG}"
DEV_URL="http://${SLUG}.wordpress.dev"


# !!! WARNING !!! #
# SLUG is not sanitized! if it contains control characters
# passed to sed this script will exploded spectacularly.
# Check your input first if there are errors

echo "Updating public path..."
pattern="s#${DEFAULT_PUBLIC_PATH}#${PUBLIC_PATH}#g;s#${DEFAULT_DEV_URL}#${DEV_URL}#g"
sed -i $pattern ./resources/assets/config.json

echo $REPLACE_TEXT_DOMAIN

if [ ${REPLACE_TEXT_DOMAIN} = "true" ]; then
    echo "Searching for files containing ${DEFAULT_SLUG}..."
    pattern="s/'${DEFAULT_SLUG}'/'${SLUG}'/g"
    git grep -lw "'${DEFAULT_SLUG}'" -- './*.php' ':!/app/lib/' | xargs sed -i $pattern

    echo "Updating style.css header..."
    sed -i -e "s/\s$DEFAULT_SLUG/ $SLUG/g" ./resources/style.css
fi

