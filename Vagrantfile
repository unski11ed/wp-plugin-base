# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure("2") do |config|

 # minimalna konfiguracja dla Vagranta. Dostosuj swój host
 config.vm.box = "scotch/box"
 config.vm.network "private_network", ip: "192.168.33.89"
 config.vm.hostname = "site.dev"
 config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]

 # dodatkowe synchronizowane katalogi
 config.vm.synced_folder "./dist", "/var/www/public/wp-content/plugins", :mount_options => ["dmode=777", "fmode=666"]

 # aktualizacja Guest Additions, aby odpowiadały tym systemowym
 # fragment opcjonalny lecz aktualizacja trwa każdorazowo ~4 minuty
 if Vagrant.has_plugin?('vagrant-vbguest')
 config.vbguest.auto_update = false
 end

 # optymalizacja i zmiana nazwy maszyny
 # fragment opcjonalny
 config.vm.provider :virtualbox do |vb|
 vb.name = config.vm.hostname
 vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
 vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
 end

 # instalacja WP-CLI
 config.vm.provision "shell", inline: <<-SHELL
 echo 'Installing WP-CLI'
 curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
 chmod +x wp-cli.phar
 sudo mv wp-cli.phar /usr/local/bin/wp
 SHELL 

 # import bazy danych
 config.vm.provision "shell", inline: <<-SHELL
 if [ -f /var/www/db/dump.sql ]; then
 echo 'Importing database...'
 mysql -u root -proot scotchbox < /var/www/db/dump.sql
 fi
 SHELL

 # tworzenie katalogu strony oraz instalacja i konfiguracja WP
 # dostosuj dane WordPressa
 # uwaga na formatowanie w argumencie --extra-php - nie może tam się pojawić znak biały! 
 config.vm.provision "shell", privileged: false, inline: <<-SHELL
 if [ ! -d /var/www/public ]; then
 mkdir /var/www/public
 fi
 cd /var/www/public
 if [ ! -f ./wp-config.php ]; then
 echo 'Downloading WordPress...'
 wp core download --force
 echo 'Configuring WordPress...'
 wp core config --dbname=scotchbox --dbuser=root --dbpass=root --dbprefix=udev_ --extra-php --extra-php <<PHP
define( 'WP_DEBUG', true );
PHP
 fi
 if ! $(wp core is-installed); then
 echo 'Installing WordPress'
 wp core install --url='site.dev' --title='Site' --admin_user=admin --admin_password=admin --admin_email='admin@site.dev' --skip-email
 wp rewrite structure '/%postname%/' --hard
 wp plugin uninstall hello --deactivate
 wp plugin uninstall akismet --deactivate
 wp theme delete twentyfourteen
 wp theme delete twentyfifteen
 fi
 SHELL

 # tworzenie pliku zrzutu bazy danych
 config.vm.provision "shell", inline: <<-SHELL
 if [ ! -f /var/www/mysqldump.sh ]; then
 touch /var/www/mysqldump.sh;
 echo "#!/bin/bash
 if [ ! -d /var/www/db ]; then
 mkdir /var/www/db
 fi

 if [ -f /var/www/db/dump.sql ]; then
 mv /var/www/db/dump.sql /var/www/db/dump-\`date +%Y-%m-%d_%H.%M.%S\`.sql;
 fi

 mysqldump -u root -proot scotchbox > /var/www/db/dump.sql;" >> /var/www/mysqldump.sh
 fi
 SHELL

 # zrzut bazy danych podczas usuwania maszyny
 config.trigger.before :destroy do
 if File.exist?( File.dirname(__FILE__) + '/mysqldump.sh' )
 info 'Dumping DB to dump.sql...'
 run_remote "bash /var/www/mysqldump.sh"
 end
 end

 # instalacja zależności WordPressa z pliku wp.sh
 config.trigger.after :up do
 if File.exist?( File.dirname(__FILE__) + '/wp.sh' )
 info 'Installing WP dependencies...'
 run_remote "bash /var/www/wp.sh"
 end
 end

 # uruchomienie MailCatchera pozwalajcego na odbiór maili
 config.vm.provision "shell", inline: "/home/vagrant/.rbenv/shims/mailcatcher --http-ip=0.0.0.0", run: "always"

end