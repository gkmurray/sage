#!/usr/bin/env bash

if [ $# -lt 1 ]; then
    echo "usage: $0 <slug> [replace-text-domain] [dev-url]"
    exit 1
fi

DEFAULT_SLUG="sage"
DEFAULT_PUBLIC_PATH="/app/themes/sage"
DEFAULT_DEV_URL="example.dev"

SLUG=$1
REPLACE_TEXT_DOMAIN=${2-false}
DEV_URL=${3-${SLUG}.wordpress.dev}
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
FILE="./resources/assets/config-local.json"
cat > $FILE <<EOF
{
  "publicPath": "${PUBLIC_PATH}",
  "devUrl": "http://${DEV_URL}"
}
EOF

if [ ${REPLACE_TEXT_DOMAIN} = "true" ]; then
    echo "Searching for files containing ${DEFAULT_SLUG}..."
    pattern="s/'${DEFAULT_SLUG}'/'${SLUG}'/g"
    git grep -lw "'${DEFAULT_SLUG}'" -- './*.php' ':!/app/lib/' | xargs sed "${ioption[@]}" $pattern

    echo "Updating style.css header..."
    sed "${ioption[@]}" -e "s/\s$DEFAULT_SLUG/ $SLUG/g" ./resources/style.css
fi

