#!/bin/bash

# 设置文档符号链接脚本
# 此脚本会在 frontend/public/docs 创建指向项目根目录 docs 的符号链接

DOCS_SOURCE="../../docs"
DOCS_TARGET="public/docs"

cd "$(dirname "$0")"

# 如果目标已存在且不是符号链接，先删除
if [ -e "$DOCS_TARGET" ] && [ ! -L "$DOCS_TARGET" ]; then
  echo "删除现有的 docs 目录..."
  rm -rf "$DOCS_TARGET"
fi

# 如果符号链接不存在，创建它
if [ ! -L "$DOCS_TARGET" ]; then
  echo "创建文档符号链接..."
  ln -s "$DOCS_SOURCE" "$DOCS_TARGET"
  echo "✅ 文档符号链接创建成功！"
  echo "   源目录: $(pwd)/$DOCS_SOURCE"
  echo "   链接位置: $(pwd)/$DOCS_TARGET"
else
  echo "✅ 文档符号链接已存在"
fi
