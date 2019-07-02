#!/bin/bash

# TOR Setup Script
# Author: Nick Busey
#
# This file is meant to get SSH access via Tor to a server in one command.
#
# Usage (as root): $ bash <(curl -s https://gitlab.com/grownetics/devops/raw/master/tor_ssh.sh)
#
# Client Config Example
#
# In order to connect to the hostnames output by this file, you should have the TOR browser running
# and your ~/.ssh/config should contain the following 2 lines:
#
#   Host *.onion
#       ProxyCommand /usr/bin/nc -xlocalhost:9150 -X5 %h %p
#
# Now once you get a hostname back from the script (e.g.: tmxybgr6e7kpenoq.onion)
# you can connect to it like a normal host.
#
# Example: `ssh vagrant@tmxybgr6e7kpenoq.onion`

# Install Tor
apt-get update && apt-get install -y tor

# Append the hidden service configuration to the Torrc file
echo -e "HiddenServiceDir /var/lib/tor/onion-ssh/\nHiddenServicePort 22 127.0.0.1:22" > /etc/tor/torrc

# Remove the bogus tor service Ubuntu installs by default
rm /lib/systemd/system/tor.service

# Ensure the changes are recognized
systemctl daemon-reload

# Restart Tor to generate the new configuration
/etc/init.d/tor restart

# Wait 30 seconds for the configuration to generate
echo "Wait 30 seconds for Tor to start and generate the hostname" && sleep 30

# Output the Hostname file contents.
echo "You can now SSH to: " && cat /var/lib/tor/onion-ssh/hostname
