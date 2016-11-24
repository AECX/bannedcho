 #!/bin/bash
echo "----------------------------------------------
Ayy welcome to the install-script for Bannedcho!
"

chmod 777 -R ./*

apt-get -y install mc apache2 libapache2-mod-php5 mysql-server php5-common php5-cli php5-mcrypt php5-mysql cron python3 python3-dev python3-pip libffi6 libffi-dev unzip phpmyadmin

echo "---------------------------------------------
Packages Installed...
Installing python addons...
"

pip3 install flask
pip3 install tornado
pip3 install psutil
pip3 install bcrypt
pip3 install pymysql

echo "---------------------------------------------
Python addons installed...
Starting to activate mods for apache2...
"

a2enmod proxy
a2enmod proxy_html
a2enmod proxy_http

echo "======================================================
>]- Thank you for choosing Bannedcho -[<
Done! Have fun with Bannedcho!"
