# Set Up 
After setting up a docker container or other virtual environment.

**Run from /freethelots/laravel**
```$xslt
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

