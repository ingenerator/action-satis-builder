#!/bin/bash
set -o nounset
set -o errexit

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
OUTPUT_PATH="$1"

echo "Compiling satis.json from dynamic package sources"
"$DIR/bin/satisfy" \
  --repofile "$DIR/satis-explicit-packages.json" \
  --packagefile "$DIR/satisfy-packagelist.json" \
  --output "$DIR/satis.json"

echo "Building repository"
"$DIR/bin/satis" build "$DIR/satis.json" "$1"

echo "Complete"
