#!/usr/bin/env bash
# Gera pedrodesign-site.zip (só o site pronto) para subir na public_html da Hostinger.
set -e
cd "$(dirname "$0")/.."
rm -f pedrodesign-site.zip
zip -qr -X pedrodesign-site.zip . -x ".git/*" "src/*" ".gitignore" "README.md" "CLAUDE.md" "pedrodesign-site.zip" "*/__pycache__/*"
echo "pronto: $(pwd)/pedrodesign-site.zip"
