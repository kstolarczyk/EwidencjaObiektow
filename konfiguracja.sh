
#				Skrypt konfigurujący system Linux Ubuntu dla aplikacji internetowej Ewidencja Obiektow Terenowych

####	Aktualizacja i dodawanie nowych repozytoriów

sudo apt-get update
#	PHP
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php


#	yarn
sudo apt -y install curl
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
sudo apt-get update



####	Instalacja Apache2, PHP7.4, MySQL, GIT

sudo apt -y install apache2
sudo apt -y install mysql-server
sudo apt -y install php7.4
sudo apt -y install php7.4-mysql
sudo apt -y install git

####	Instalacja  Composer, Yarn, NodeJS, PECL

sudo mv composer.phar /usr/local/bin/composer
sudo curl -s https://getcomposer.org/installer | php
sudo apt -y install composer
sudo apt -y install yarn
sudo apt -y install nodejs
sudo apt -y install php-pear
sudo apt -y install libssl-dev php7.4-dev
sudo pecl install xdebug

#### Konfiguracja PHP oraz Apache2

sudo echo zend_extension=/usr/lib/php/20190902/xdebug.so >> /etc/php/7.4/apache2/php.ini

sudo cat <<EOT >> /etc/apache2/apache2.conf
Alias /EwidencjaObiektow "/var/www/EwidencjaObiektow/public/"

<Directory /var/www/EwidencjaObiektow/public/>
	Options Indexes FollowSymLinks
	AllowOverride None
	Require all granted
</Directory>
EOT

service apache2 restart

####	Konfiguracja bazy danych

sudo mysql -u root < ./konfiguracja.sql

#### Pobranie aplikacji

cd /var/www
sudo git clone https://github.com/kstolarczyk/EwidencjaObiektow.git

#### Konfiguracja Aplikacji 

cd EwidencjaObiektow
sudo yarn install
sudo yarn encore dev
sudo php bin/console composer.phar dump-autoload -o -a
sudo composer require doctrine/doctrine-migrations-bundle "^3.0"
sudo php bin/console doctrine:schema:create
sudo php bin/console cache:clear




