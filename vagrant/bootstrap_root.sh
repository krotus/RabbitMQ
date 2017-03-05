#!/usr/bin/env bash

apt update
apt install -y php php-mbstring php-xdebug php-xml zip unzip composer doxygen graphviz

# Place a color prompt into the ssh terminal.

sed -i "s/#force_color_prompt=yes/force_color_prompt=yes/g" /home/vagrant/.bashrc