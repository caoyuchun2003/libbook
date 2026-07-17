#!/usr/bin/env bash
set -euo pipefail
RAW="${1:-}"
if [[ -z "$RAW" ]]; then
  echo "用法: $0 'https://xxxx.cfc-execute.bj.baidubce.com'"
  exit 1
fi
BASE="${RAW%/}"
if [[ "$BASE" == */libbook/api ]]; then
  VALUE="$BASE"
else
  VALUE="${BASE}/libbook/api"
fi
echo "Setting VITE_LIBBOOK_API_BASE=$VALUE"
gh secret set VITE_LIBBOOK_API_BASE --repo caoyuchun2003/libbook --body "$VALUE"
echo "OK. 触发 Pages："
echo "  gh workflow run 'Deploy frontend to GitHub Pages' --repo caoyuchun2003/libbook"
