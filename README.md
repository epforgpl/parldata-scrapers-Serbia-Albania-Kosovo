# Scrapers of parliamentary data 
## For Serbia, Albania and Kosovo

### Requirements
- Cake 2.6.1
- [Poppler](http://poppler.freedesktop.org/)
- `sudo apt-get install poppler-utils`
- [Unoconv](http://dag.wiee.rs/home-made/unoconv/): `sudo apt-get install unoconv`
- MySQL

### Installation
1. Create directory for those scrapers

   ```
mkdir /home/scrapers/sak
cd /home/scrapers/sak
   ```

1. Download [Cake 2.6.1](https://github.com/cakephp/cakephp/archive/2.6.1.tar.gz) and extract it

   ```
cd /home/scrapers/
curl -kL https://github.com/cakephp/cakephp/archive/2.6.1.tar.gz | tar xvz
mv cakephp-2.6.1 serbia-albania-kosovo
   ```

1. Delete app folder contents and pull there this repo

   ```
cd serbia-albania-kosovo 
rm -r app
git clone https://github.com/contributors-kodujdlapolski-pl/parldata-scrapers-Serbia-Albania-Kosovo app
   ```

1. Give rights to files

   ```
chown USER:USER -R .
chmod -R g-w .
chmod -R g+w app/webroot
sudo adduser www-data USER
   ```

1. Create database and config in `app/Config/database.php`

   ```
cp app/Config/database.php.default app/Config/database.php
   ```

1. Create database user & pass
   
   ```
mysql -u root -p -e "CREATE DATABASE scrapers_sak; GRANT ALL PRIVILEGES ON scrapers_sak.* TO scrapers_sak@localhost IDENTIFIED BY 'scrapers_sak';"
   ```

1. Load DB or create from schema

   ```
cd app/tmp
unrar x sql_scrapper\ 20150212\ 0226.rar
cat sql_scrapper\ 20150212\ 0226.sql | mysql -u scrapers_sak -p   
   
# Console/cake schema create
# Console/cake schema update
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
