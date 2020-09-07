#!/bin/ash
set -o nounset
set -o errexit

echo "Creating composer working directory with auth token"
mkdir $COMPOSER_HOME
echo  "{\"github-oauth\": {\"github.com\": \"$GITHUB_TOKEN\"}}" > $COMPOSER_HOME/auth.json

export COMPOSER_CACHE_DIR="$GITHUB_WORKSPACE/$CACHE_DIR"

echo "Compiling satis.json from dynamic package sources"
"/repo-builder/vendor/bin/satisfy" \
  --repofile "$GITHUB_WORKSPACE/satis-explicit-packages.json" \
  --packagefile "$GITHUB_WORKSPACE/satisfy-packagelist.json" \
  --output "$GITHUB_WORKSPACE/satis.json"

echo "Building repository"
"/repo-builder/vendor/bin/satis" build "$GITHUB_WORKSPACE/satis.json" "$GITHUB_WORKSPACE/satis_output"

echo "Complete"
