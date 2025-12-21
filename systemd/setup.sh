#!/bin/bash
# systemd/setup.sh
# Setup systemd services via symlink

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SYSTEMD_DIR="$PROJECT_DIR/systemd"
USER_SYSTEMD_DIR="$HOME/.config/systemd/user"

echo "Setting up systemd services..."
echo "Project: $PROJECT_DIR"
echo ""

# Create user systemd directory
mkdir -p "$USER_SYSTEMD_DIR"

# Create temporary directory for processed service files
TEMP_DIR="$SYSTEMD_DIR/.tmp"
mkdir -p "$TEMP_DIR"

# Process and copy service files with PROJECT_DIR substitution
for file in "$SYSTEMD_DIR"/*.service "$SYSTEMD_DIR"/*.timer "$SYSTEMD_DIR"/*.target; do
    [ -e "$file" ] || continue
    filename=$(basename "$file")

    # Skip if it's in temp directory
    [[ "$file" == *"/.tmp/"* ]] && continue

    # Replace PROJECT_DIR_PLACEHOLDER with actual project directory
    sed "s|PROJECT_DIR_PLACEHOLDER|$PROJECT_DIR|g" "$file" > "$TEMP_DIR/$filename"

    # Create symlink
    ln -sf "$TEMP_DIR/$filename" "$USER_SYSTEMD_DIR/$filename"
    echo "Linked: $filename"
done

# Reload systemd daemon
systemctl --user daemon-reload

echo ""
echo "Setup complete."
echo ""
echo "Next steps:"
echo "  1. sudo loginctl enable-linger \$USER"
echo "  2. systemctl --user start jamurcare.target"
echo "  3. systemctl --user enable jamurcare.target"
echo ""
echo "Control all services with single command:"
echo "  Start:   systemctl --user start jamurcare.target"
echo "  Stop:    systemctl --user stop jamurcare.target"
echo "  Restart: systemctl --user restart jamurcare.target"
echo "  Status:  systemctl --user status jamurcare.target"
