# Kiosk Management System based on Laravel

This is a training project, that will be user to manage a "kiosks" display system.
The purpose is to allow kiosks administrators to add/remove presentation files with a clean an simple interface.
It replaces the files managements based on the samba mount, and manual kiosks parameters files edition.

This should be the base for future devs.

# Requirements

The project is to be run on any Web server, as soon as Laravel requirements are set.
The initial installation will be done on a Synology NAS. Thus the installation process is given for that environment.

https://laravel.com/docs/7.x/installation#server-requirements

## Composer Install 

I simply followed the proceditre given here : https://aradaff.com/composer-sur-dsm-synology/

The commands where adapted to use PHP 7.3.

1. The required extensions are now loaded in the file /usr/local/etc/php73/cli/conf.d/extension.ini 
````
sudo vi /usr/local/etc/php73/cli/conf.d/extension.ini 
`````

I just double checked that the option added with the PHP management interface of the syno where present in that file.
I encoutered Warning with previous setup using obsolete libs in the process.
````
PHP Startup: Unable to load dynamic library 'mysql.so' 
PHP Startup: Unable to load dynamic library 'mcrypt.so' 
PHP Startup: Unable to load dynamic library 'mysql.so'
````

The were caused by other php.ini files located there : /usr/local/etc/php73/cli/conf.d/
The fix is simple; comment the incrimated libs in theses config files.

2. Install Git (client)

On the synology, add the community package repo, dans Packages / Paramétres / Sources de paquet.

Add the source: 

&nbsp;&nbsp;Nom : Synocommunity

&nbsp;&nbsp;Emplacement : https://packages.synocommunity.com/


Next, find git in the Community, and install it. Easy !!!

3. Clone the app

Vias ssh, go in the Webstation dir and clone the repo :

```
cd /volume2/web/
git clone https://github.com/patriceB91/laravel5.git
```

4. Get the composer dependances

````
cd /volume2/web/laravel5
composer install
````

5. Generate the app key :

````
php73 artisan key:generate
````

** To run artisan, one must be in the project dir.



6. Add Config

Since .env and storage files are not part of the repo, they need to be placed manualy.

Put the custmized .env file in the root path of the app.
Create a 


7. Align permissions.

Run the following commands on all concerned dirs :
````
sudo chown -R $USER:http dir
chmod -R 775 dir 
`````

Where dir represent the dir to be updated.

Typically on the following dirs :

- public
- bootstrap cache
- storage

!! Dont forget to update when adding files...

!! The following commande on the app dir helped a lot !

````
sudo chmod 755 -R kiosksadmin
````

# Biblio

https://oshara.ca/fr/blog/comment-installer-une-application-web-laravel-que-vous-avez-clone-depuis-git

https://community.synology.com/enu/forum/1/post/133463

https://tech.setepontos.com/2017/05/21/installing-php-composer-on-a-synology-nas/
