# Systemd Services Management Guide

## Quick Setup

```bash
# Run setup script
chmod +x systemd/setup.sh
./systemd/setup.sh

# Enable linger
sudo loginctl enable-linger $USER

# Start all services
systemctl --user start jamurcare.target

# Enable auto-start
systemctl --user enable jamurcare.target
```

---

## Single Command Control (Recommended)

Control all services with one target:

```bash
# Start all
systemctl --user start jamurcare.target

# Stop all
systemctl --user stop jamurcare.target

# Restart all
systemctl --user restart jamurcare.target

# Status all
systemctl --user status jamurcare.target

# Enable auto-start
systemctl --user enable jamurcare.target

# Disable auto-start
systemctl --user disable jamurcare.target
```

---

## Individual Service Control

You can still control services individually if needed:

```bash
# Start/Stop/Restart individual service
systemctl --user start jamurcare-mqtt
systemctl --user stop jamurcare-reverb
systemctl --user restart jamurcare-queue

# Status
systemctl --user status jamurcare-mqtt
```

---

## Monitoring & Logs

### View Logs (Real-time)
```bash
# Individual service
journalctl --user -u jamurcare-mqtt -f
journalctl --user -u jamurcare-reverb -f
journalctl --user -u jamurcare-queue -f
journalctl --user -u jamurcare-aggregate -f

# All services combined
journalctl --user -u jamurcare-mqtt -u jamurcare-reverb -u jamurcare-queue -f
```

### View Logs (Historical)
```bash
# Last 100 lines
journalctl --user -u jamurcare-mqtt -n 100

# Since specific time
journalctl --user -u jamurcare-mqtt --since "1 hour ago"
journalctl --user -u jamurcare-mqtt --since today

# Error logs only
journalctl --user -u jamurcare-mqtt -p err
```

---

## Timer Management

```bash
# Check timer status
systemctl --user status jamurcare-aggregate.timer

# List all timers
systemctl --user list-timers

# Manually trigger timer
systemctl --user start jamurcare-aggregate.service
```

---

## Troubleshooting

### Service Won't Start
```bash
# Check status
systemctl --user status jamurcare-mqtt

# Check logs
journalctl --user -u jamurcare-mqtt -n 50 --no-pager

# Verify service file
systemd-analyze verify ~/.config/systemd/user/jamurcare-mqtt.service

# Test command manually
cd /path/to/jamurcare
php artisan mqtt:subscribe
```

### Service Not Running After Reboot
```bash
# Enable linger
sudo loginctl enable-linger $USER

# Check linger status
loginctl show-user $USER | grep Linger

# Enable target
systemctl --user enable jamurcare.target
```

### After Code Changes
```bash
# Restart all services
systemctl --user restart jamurcare.target

# Or restart individual service
systemctl --user restart jamurcare-queue
```

---

## After Editing Service Files

```bash
# Reload systemd daemon
systemctl --user daemon-reload

# Restart services
systemctl --user restart jamurcare.target
```

---

## Configuration Files

```
~/.config/systemd/user/
├── jamurcare.target -> ~/jamurcare/systemd/.tmp/jamurcare.target
├── jamurcare-mqtt.service -> ~/jamurcare/systemd/.tmp/jamurcare-mqtt.service
├── jamurcare-reverb.service -> ~/jamurcare/systemd/.tmp/jamurcare-reverb.service
├── jamurcare-queue.service -> ~/jamurcare/systemd/.tmp/jamurcare-queue.service
├── jamurcare-monitoring-aggregate.service -> ~/jamurcare/systemd/.tmp/jamurcare-monitoring-aggregate.service
└── jamurcare-monitoring-aggregate.timer -> ~/jamurcare/systemd/.tmp/jamurcare-monitoring-aggregate.timer
```

---

## Common Tasks

```bash
# Check if all services are running
systemctl --user is-active jamurcare.target

# View resource usage
systemd-cgtop

# Restart count
systemctl --user show jamurcare-queue --property=NRestarts

# Check next timer run
systemctl --user list-timers jamurcare-aggregate.timer
```

---

## Enable Linger (Important!)

```bash
# Enable linger (services run without login)
sudo loginctl enable-linger $USER

# Verify
loginctl show-user $USER | grep Linger
```

