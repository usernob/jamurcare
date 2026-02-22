#!/bin/bash
# systemd/setup.sh
# Setup systemd services via symlink

set -e

ACTION="${1:-install}"
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SYSTEMD_DIR="$PROJECT_DIR/systemd"
DEST_DIR="/etc/systemd/system"
TEMP_DIR="$SYSTEMD_DIR/.tmp"


install_services() {
    echo "Setting up systemd services..."
    echo "Project dir: $PROJECT_DIR"

    # Create temporary directory for processed service files
    mkdir -p "$TEMP_DIR"

    # Process and copy service files with PROJECT_DIR substitution
    for file in "$SYSTEMD_DIR"/*.service "$SYSTEMD_DIR"/*.timer "$SYSTEMD_DIR"/*.target; do
        [ -e "$file" ] || continue
        filename=$(basename "$file")

        # Replace PROJECT_DIR_PLACEHOLDER with actual project directory
        sed "s|PROJECT_DIR_PLACEHOLDER|$PROJECT_DIR|g" "$file" > "$TEMP_DIR/$filename"

        # Create symlink
        sudo ln -sf "$TEMP_DIR/$filename" "$DEST_DIR/$filename"
        echo "Linked: $filename"
    done

    # Reload systemd daemon
    sudo systemctl daemon-reload

    echo "Setup complete."
    echo "Control all services with single target: jamurcare.target"
    echo "Control dummy iot service: jamurcare-dummy-iot.service"
}

remove_services() {
    echo "Removing services..."

    for file in "$SYSTEMD_DIR"/*.service "$SYSTEMD_DIR"/*.timer "$SYSTEMD_DIR"/*.target; do
        [ -e "$file" ] || continue

        filename=$(basename "$file")

        echo "Removing $filename"

        sudo systemctl stop "$filename" || true
        sudo systemctl disable "$filename" || true

        sudo rm -f "$DEST_DIR/$filename"
    done

    sudo systemctl daemon-reload

    echo "Remove complete"
}

case "$ACTION" in

install)
    install_services
    ;;

remove)
    remove_services
    ;;

*)
    echo "Usage:"
    echo "  $0 install"
    echo "  $0 remove"
    exit 1
    ;;

esac
