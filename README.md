# Scrapers of parliamentary data 
## For Serbia, Albania and Kosovo

### Requirements
- Cake 2.6.1
- [Poppler](http://poppler.freedesktop.org/) 0.12.4
- [Unoconv](http://dag.wiee.rs/home-made/unoconv/): 0.5
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
chmod -R g+w app/tmp
sudo adduser www-data USER
   ```

1. Create database and config in `app/Config/database.php`

   ```
cp app/Config/database.php.default app/Config/database.php
   ```

1. Create database user & pass
   
   ```
mysql -u root -p -e "CREATE DATABASE scrapers_sak DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci; GRANT ALL PRIVILEGES ON scrapers_sak.* TO scrapers_sak@localhost IDENTIFIED BY 'scrapers_sak';"
   ```

1. Load DB or create from schema

   ```
cd app/tmp
unrar x sql_scrapper\ 20150212\ 0226.rar
cat sql_scrapper\ 20150212\ 0226.sql | mysql -u scrapers_sak -p   

# Clear 'sent to API' flags
mysql -u scrapers_sak -p -e "USE scrapers_sak; UPDATE quele_to_sends SET status=0;"
   
# Or start from scrach
# mysql -p -u scrapers_sak scrapers_sak < Config/Schema/scrapper.sql
# mysql -p -u scrapers_sak scrapers_sak < Config/Schema/schedules.sql 
# mysql -p -u scrapers_sak scrapers_sak < Config/Schema/albania_chambers.sql

# Go back
cd ../..
   ```

1. Update API user & pass

   ```
cp app/Config/config.php.default app/Config/config.php
vim app/Config/config.php
# Super user can log to web panel
   ```

1. Set up cron: `sude -u USER crontab -e`

   ```
/5 * * * * cd /home/scrapers/serbia-albania-kosovo/app && Console/cake schedule
   ```
