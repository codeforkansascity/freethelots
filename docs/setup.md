# Set Up 

###PHP / Composer Setup

First we need to install php and composer

####Windows-

Follow instructions [here](http://kizu514.com/blog/install-php7-and-composer-on-windows-10/)

Alternatively you can use [mamp](https://www.mamp.info/en/downloads/) or
 [xampp](https://www.apachefriends.org/index.html) and PHP will be installed automatically

####Linux

#####Ubuntu- 

PHP
```
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install php7.1 php7.1-mcrypt php7.1-mysql php7.1-mbstring php7.1-pgsql php7.1-opcache
```

Composer
```angular2html
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer 
```

####Project Setup

**Clone Repository**
```angular2html
git clone git@github.com:codeforkansascity/freethelots.git
```

**Install composer dependencies**
```$xslt
cd freethelots/app
composer install
```
**Copy the contents of .env.example to a new file called .env**

to set up the database run
```$xslt
php artisan migrate
```

**To create seed data run**
```$xslt
php artisan db:seed
```

To run app in browser
```angular2html
php artisan serve
```

This will run by default in [127.0.0.1:8000](127.0.0.1:8000)


#Routes
**All routes return json formatted data**

**/parcels**
 all parcels
 
**/parcels/search/{name}**
  searches for parcels matching name
  
**/parties**
    all parties

**/parties/search/{name}**
Search for parties matching name

