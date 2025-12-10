#!/bin/bash
################################################################################
# Regenerate stasis-app/.env file with current credentials
################################################################################

INSTALL_DIR="/var/www/html/adial"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "=== Regenerating stasis-app/.env file ==="
echo ""

# Read current database credentials
if [ -f "${INSTALL_DIR}/application/config/database.php" ]; then
    DB_USER=$(grep "'username' =>" "${INSTALL_DIR}/application/config/database.php" | sed "s/.*'username' => '\([^']*\)'.*/\1/")
    DB_PASS=$(grep "'password' =>" "${INSTALL_DIR}/application/config/database.php" | sed "s/.*'password' => '\([^']*\)'.*/\1/")
    DB_NAME=$(grep "'database' =>" "${INSTALL_DIR}/application/config/database.php" | sed "s/.*'database' => '\([^']*\)'.*/\1/")
    echo -e "${GREEN}✓${NC} Database credentials read from database.php"
else
    echo -e "${RED}✗${NC} database.php not found"
    exit 1
fi

# Read current ARI credentials
if [ -f "${INSTALL_DIR}/application/config/ari.php" ]; then
    ARI_USER=$(grep "\$config\['ari_username'\]" "${INSTALL_DIR}/application/config/ari.php" | sed "s/.*'\([^']*\)';.*/\1/")
    ARI_PASS=$(grep "\$config\['ari_password'\]" "${INSTALL_DIR}/application/config/ari.php" | sed "s/.*'\([^']*\)';.*/\1/")
    echo -e "${GREEN}✓${NC} ARI credentials read from ari.php"
else
    echo -e "${RED}✗${NC} ari.php not found"
    exit 1
fi

# Generate .env file
if [ -d "${INSTALL_DIR}/stasis-app" ]; then
    cat > "${INSTALL_DIR}/stasis-app/.env" << EOF
# Asterisk ARI Configuration
ARI_HOST=127.0.0.1
ARI_PORT=8088
ARI_USERNAME=${ARI_USER}
ARI_PASSWORD=${ARI_PASS}
ARI_APP_NAME=dialer

# MySQL Database Configuration
DB_HOST=127.0.0.1
DB_USER=${DB_USER}
DB_PASSWORD=${DB_PASS}
DB_NAME=${DB_NAME}

# Application Settings
DEBUG_MODE=true
LOG_LEVEL=debug
RECORDINGS_PATH=/var/spool/asterisk/monitor/adial
SOUNDS_PATH=/var/lib/asterisk/sounds/dialer
EOF
    chmod 600 "${INSTALL_DIR}/stasis-app/.env"
    chown asterisk:asterisk "${INSTALL_DIR}/stasis-app/.env" 2>/dev/null || true

    echo -e "${GREEN}✓${NC} .env file generated at: ${INSTALL_DIR}/stasis-app/.env"
    echo ""
    echo "Configuration:"
    echo "  Database: ${DB_USER}@localhost/${DB_NAME}"
    echo "  ARI User: ${ARI_USER}"
    echo ""
else
    echo -e "${RED}✗${NC} stasis-app directory not found"
    exit 1
fi
