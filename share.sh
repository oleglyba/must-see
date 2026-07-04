#!/bin/bash

WP_PATH="/Users/oleh/PhpstormProjects/first/wordpress"
WP_CLI="/Users/oleh/PhpstormProjects/first/wp-cli.phar"
LOCAL_URL="http://first.test"

echo "Запуск ngrok..."
pkill ngrok 2>/dev/null
sleep 1

ngrok http 80 --host-header="first.test" --log=stdout > /tmp/ngrok.log 2>&1 &
sleep 4

PUBLIC_URL=$(curl -s http://localhost:4040/api/tunnels | python3 -c "import sys,json; d=json.load(sys.stdin); print(d['tunnels'][0]['public_url'])" 2>/dev/null)

if [ -z "$PUBLIC_URL" ]; then
  echo "Помилка: не вдалось отримати URL від ngrok"
  exit 1
fi

echo "Оновлення WordPress URL..."
php "$WP_CLI" option update siteurl "$PUBLIC_URL" --path="$WP_PATH"
php "$WP_CLI" option update home "$PUBLIC_URL" --path="$WP_PATH"

echo ""
echo "✓ Сайт доступний за адресою:"
echo "  $PUBLIC_URL"
echo ""
echo "Натисніть Enter щоб зупинити і повернути все назад..."
read

echo "Відновлення локального URL..."
php "$WP_CLI" option update siteurl "$LOCAL_URL" --path="$WP_PATH"
php "$WP_CLI" option update home "$LOCAL_URL" --path="$WP_PATH"
pkill ngrok
echo "Готово. Локальний сайт відновлено."
