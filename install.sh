#!/bin/bash
set -e

echo "Instalando PU1DES Network Control..."

apt update
apt install apache2 php php-sqlite3 sqlite3 jq curl git tree -y

mkdir -p /opt/pu1des-reflector
mkdir -p /usr/local/etc/svxlink
mkdir -p /var/www/html

cp -r web/* /var/www/html/
cp config/svxreflector.conf /usr/local/etc/svxlink/svxreflector.conf
cp systemd/svxreflector.service /etc/systemd/system/svxreflector.service

cp scripts/coletar_status.php /opt/pu1des-reflector/scripts/ 2>/dev/null || true
cp scripts/gerar_config.php /opt/pu1des-reflector/scripts/ 2>/dev/null || true

mkdir -p /opt/pu1des-reflector/{config,web,systemd,scripts,logs,data,backup}

cp -r config/* /opt/pu1des-reflector/config/
cp -r web/* /opt/pu1des-reflector/web/
cp -r scripts/* /opt/pu1des-reflector/scripts/
cp -r systemd/* /opt/pu1des-reflector/systemd/

if [ ! -f /opt/pu1des-reflector/data/database.sqlite ]; then
    cp data/database.sqlite /opt/pu1des-reflector/data/database.sqlite
fi

cat > /etc/systemd/system/pu1des-coletor.service <<'SERVICE'
[Unit]
Description=PU1DES Status Collector

[Service]
Type=oneshot
ExecStart=/usr/bin/php /opt/pu1des-reflector/scripts/coletar_status.php
SERVICE

cat > /etc/systemd/system/pu1des-coletor.timer <<'TIMER'
[Unit]
Description=Executa coletor PU1DES a cada 5 segundos

[Timer]
OnBootSec=10
OnUnitActiveSec=5
Unit=pu1des-coletor.service

[Install]
WantedBy=timers.target
TIMER

chown -R www-data:www-data /opt/pu1des-reflector/data
chmod -R 775 /opt/pu1des-reflector/data

systemctl daemon-reload
systemctl enable apache2
systemctl restart apache2

systemctl enable pu1des-coletor.timer
systemctl restart pu1des-coletor.timer

echo "Instalação concluída."
echo "Painel: http://IP_DO_SERVIDOR/"
