#!/usr/bin/env bash
set -euo pipefail
export PATH="${HOME}/Library/Python/3.9/bin:${HOME}/.local/bin:${PATH}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if ! command -v bsam >/dev/null 2>&1; then
  echo "未找到 bsam。pip3 install --user bce-sam-cli 'werkzeug<3'"
  exit 1
fi
if [[ ! -f "${HOME}/.bce/credentials" ]]; then
  echo "未找到 ~/.bce/credentials。先 bsam config --ak AK --sk SK --region bj"
  exit 1
fi

echo ">>> bsam package"
bsam package
echo ">>> bsam deploy"
bsam deploy

echo
echo "部署完成。下一步："
echo "  1) 控制台复制 HTTP 触发器根地址"
echo "  2) ./scripts/set-github-secret.sh 'https://xxxx.cfc-execute.bj.baidubce.com'"
echo "  3) curl -s https://xxxx.cfc-execute.bj.baidubce.com/libbook/api/books"
echo "  4) Actions 跑 Deploy frontend to GitHub Pages"
