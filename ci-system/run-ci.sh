#!/usr/bin/env bash
cd /var/www/bannedcho/ci-system
nohup bash 'ci-system.sh' & 2>&1 >/dev/null
