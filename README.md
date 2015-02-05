# Scrapers of parliamentary data 
## For Serbia, Albania and Kosovo

### Requirements
- Cake 2.6.1
- [Poppler](http://poppler.freedesktop.org/)
- `sudo apt-get install poppler-utils`
- [Unoconv](http://dag.wiee.rs/home-made/unoconv/): `sudo apt-get install unoconv`

### Installation
1. Download [Cake 2.6.1](https://github.com/cakephp/cakephp/archive/2.6.1.tar.gz) and extract it

   ```
wget https://github.com/cakephp/cakephp/archive/2.6.1.tar.gz | tar -xvf
   ```

1. Delete app folder contents and pull there this repo

   ```
cd TODO && rm -r app/*
git clone https://github.com/contributors-kodujdlapolski-pl/parldata-scrapers-Serbia-Albania-Kosovo app
   ```

1. Give rights to files

   ```
mkdir app/webroot/kosovo app/webroot/albania app/webroot/serbia
chmod -R o+w app/webroot
   ```

1. Create database and config in `app/Config/database.php`

   ```
cp app/Config/database.php.default app/Config/database.php
vim app/Config/database.php
   ```

1. Update API user & pass

   ```
cp app/Config/config.php.default app/Config/config.php
vim app/Config/config.php
   ```

1. Set up cron: `crontab -e`

   ```
/5 * * * * cd /home/scrapers/sak/app && Console/cake schedule
   ```
