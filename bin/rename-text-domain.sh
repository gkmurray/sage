#!/usr/bin/env bash

if [ $# -lt 1 ]; then
    echo "usage: $0 <slug> [dev-url] [replace-text-domain]"
    exit 1
fi

DEFAULT_SLUG="sage"
DEFAULT_PUBLIC_PATH="/app/themes/sage"
DEFAULT_DEV_URL="example.dev"

SLUG=$1
DEV_URL=${2-${SLUG}.wordpress.dev}
REPLACE_TEXT_DOMAIN=${3-false}
PUBLIC_PATH="/wp-content/themes/${SLUG}"

# portable in-place argument for both GNU sed and Mac OSX sed
if [[ $(uname -s) == 'Darwin' ]]; then
    ioption=('-i' '')
else
    ioption=('-i')
fi

# !!! WARNING !!! #
# SLUG is not sanitized! if it contains control characters
# passed to sed this script will exploded spectacularly.
# Check your input first if there are errors

echo "Updating public path..."
pattern="s#${DEFAULT_PUBLIC_PATH}#${PUBLIC_PATH}#g;s#${DEFAULT_DEV_URL}#${DEV_URL}#g"
sed "${ioption[@]}" $pattern ./resources/assets/config.json

if [ ${REPLACE_TEXT_DOMAIN} = "true" ]; then
    echo "Searching for files containing ${DEFAULT_SLUG}..."
    pattern="s/'${DEFAULT_SLUG}'/'${SLUG}'/g"
    git grep -lw "'${DEFAULT_SLUG}'" -- './*.php' ':!/app/lib/' | xargs sed "${ioption[@]}" $pattern

    echo "Updating style.css header..."
    sed "${ioption[@]}" -e "s/\s$DEFAULT_SLUG/ $SLUG/g" ./resources/style.css
fi

