#!/bin/bash

set -euo pipefail

ROOT="/home/prro3328/akashaproduction"
HOST="https://scille.o2switch.net:2083"
USER_NAME="prro3328"

read -s -p "cPanel password: " PASS
echo

while IFS= read -r dir; do
  [ "$dir" = "." ] && continue
  current="$ROOT"
  IFS='/' read -r -a parts <<< "$dir"
  for part in "${parts[@]}"; do
    curl -s -u "$USER_NAME:$PASS" -G "$HOST/json-api/cpanel" \
      --data-urlencode "cpanel_jsonapi_user=$USER_NAME" \
      --data-urlencode "cpanel_jsonapi_apiversion=2" \
      --data-urlencode "cpanel_jsonapi_module=Fileman" \
      --data-urlencode "cpanel_jsonapi_func=mkdir" \
      --data-urlencode "path=$current" \
      --data-urlencode "name=$part" >/dev/null || true
    current="$current/$part"
  done
done < <(git ls-files | xargs -n1 dirname | awk '!seen[$0]++')

while IFS= read -r file; do
  dir=$(dirname "$file")
  base=$(basename "$file")
  remote_dir="$ROOT"
  if [ "$dir" != "." ]; then
    remote_dir="$ROOT/$dir"
  fi

  echo "Uploading $file"
  curl -s -u "$USER_NAME:$PASS" -G "$HOST/execute/Fileman/save_file_content" \
    --data-urlencode "dir=$remote_dir" \
    --data-urlencode "file=$base" \
    --data-urlencode "content@$file" >/dev/null
done < <(git ls-files)

echo "SYNC_DONE"
