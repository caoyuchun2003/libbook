@echo off
REM Windows 批处理脚本：设置文档符号链接

set DOCS_SOURCE=..\..\docs
set DOCS_TARGET=public\docs

cd /d "%~dp0"

REM 如果目标已存在且不是符号链接，先删除
if exist "%DOCS_TARGET%" (
    echo 删除现有的 docs 目录...
    rmdir /s /q "%DOCS_TARGET%"
)

REM 创建符号链接（需要管理员权限）
echo 创建文档符号链接...
mklink /D "%DOCS_TARGET%" "%DOCS_SOURCE%"

if %errorlevel% equ 0 (
    echo ✅ 文档符号链接创建成功！
    echo    源目录: %CD%\%DOCS_SOURCE%
    echo    链接位置: %CD%\%DOCS_TARGET%
) else (
    echo ❌ 创建符号链接失败
    echo    提示: 在 Windows 上创建符号链接可能需要管理员权限
    echo    请以管理员身份运行此脚本，或手动执行:
    echo    mklink /D "%DOCS_TARGET%" "%DOCS_SOURCE%"
)

pause
