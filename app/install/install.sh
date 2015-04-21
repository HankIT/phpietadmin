#!/bin/bash

# Install bins
apt-get install -y build-essential iscsitarget iscsitarget-dkms apache2 sudo libapache2-mod-php5 linux-headers-3.2.0-4-amd64

# Create sudoers file
cat > /etc/sudoers.d/phpietadmin << "EOF"
            www-data ALL=NOPASSWD: /usr/sbin/service iscsitarget *, /sbin/vgs, /sbin/pvs, /sbin/lvs, /bin/lsblk -rn, /usr/sbin/ietadm --op *, /sbin/lvcreate, /sbin/lvremove -f *, /sbin/lvextend, /sbin/lvreduce
EOF

# Set permissions for the iet config files and phpietadmin dir
chown -R www-data:www-data /usr/share/phpietadmin
chown -R root:www-data /etc/iet
chmod -R 660 /etc/iet

# Secure installation
sed -i '/ALL ALL/d' /etc/iet/initiators.allow

# Configure apache
sed -i 's/None/all/g' /etc/apache2/sites-enabled/000-default
echo "Alias /usr/share/phpietadmin/public /phpietadmin" >> /etc/apache2/sites-enabled/000-default

# Restart services
service iscsitarget restart
service apache2 restart

# Create database and change permissions
sqlite3 ../config.db < database.sql
chown -R www-data:www-data ../config.db
chmod 660 ../config.db

# Read password from database
read -s -p "Enter password for user admin: " pass

# Write password to database
sqlite3 ../config.db "INSERT INTO user (username, password) values (\"admin\", \"$pass\");"

echo
echo "Done!"