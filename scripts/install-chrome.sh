#!/usr/bin/env bash

wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
dpkg -i google-chrome-stable_current_amd64.deb
apt-get install -f -y
rm google-chrome-stable_current_amd64.deb
