#!/bin/bash
# Auto-init script: install deps + import DB if missing
set -e

# Install packages if not present (idempotent)
if ! command -v apache2 >/dev/null 2>&1 || ! command -v php >/dev/null 2>&1 || ! command -v mariadbd >/dev/null 2>&1; then
    DEBIAN_FRONTEND=noninteractive apt-get update -qq
    DEBIAN_FRONTEND=noninteractive apt-get install -y -qq \
        apache2 php php-mysql php-cli libapache2-mod-php php-mbstring \
        mariadb-server mariadb-client > /dev/null 2>&1
fi

# Apache config
cat > /etc/apache2/ports.conf <<'EOF'
Listen 3000
EOF
cat > /etc/apache2/sites-enabled/000-default.conf <<'EOF'
<VirtualHost *:3000>
    ServerAdmin webmaster@localhost
    DocumentRoot /app
    <Directory /app>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

mkdir -p /var/run/apache2 /run/mysqld /var/log/apache2
chown -R mysql:mysql /run/mysqld /var/lib/mysql 2>/dev/null || true

# Wait for MariaDB to be ready (it's managed by supervisor separately)
for i in {1..20}; do
    if mysqladmin -uroot ping >/dev/null 2>&1; then break; fi
    sleep 1
done

# Import database if not present
if ! mysql -uroot -e "USE db_anemia" >/dev/null 2>&1; then
    if [ -f /app/db_anemia.sql ]; then
        mysql -uroot < /app/db_anemia.sql
    fi
fi

# Ensure MySQL user 'anemia' exists
mysql -uroot -e "CREATE USER IF NOT EXISTS 'anemia'@'localhost' IDENTIFIED BY 'anemia_pass'; GRANT ALL PRIVILEGES ON db_anemia.* TO 'anemia'@'localhost'; FLUSH PRIVILEGES;" >/dev/null 2>&1

echo "[init] done"
