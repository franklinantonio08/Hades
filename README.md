
/* Elimina la carpeta del Proyecto Hades */ 
rm -rf /var/www/html/Hades

/* Baja del Git el proyecto Completo */
git clone https://github.com/franklinantonio08/Hades.git

/* Genera el token en el git y agregado desde el ghp hasta el @ */
git clone https://franklinantonio08:_8nz05H49msbjy256ndtO9zLT2ACNTX4JB7C0@github.com/franklinantonio08/Hades.git

/* set el remote para hacerle pull al git*/
<!-- git remote set-url origin https://franklinantonio08:_8nz05H49msbjy256ndtO9zLT2ACNTX4JB7C0@github.com/franklinantonio08/Hades.git -->

/*Esto de hace  para poner el global del github*/
<!-- composer config --global --auth github-oauth.github.com _PAB6eOTESNdOalmFKfLGxO1lWU9NC42LCSoe -->

/* Entramos al directorio del proyecto*/
cd Hades

/* Actualizamos el Composer */
composer update 

/* copia el example.env a .env */ 
mv .env.example .env
chmod 600 .env
chown apache:apache .env  

/* Crea Carpetas Publicas*/
mkdir -p /var/www/html/Hades/storage/app/public/export_temp
mkdir -p /var/www/html/Hades/storage/app/public/infractores
mkdir -p /var/www/html/Hades/storage/app/public/movimientos
mkdir -p /var/www/html/Hades/storage/app/public/multas
mkdir -p /var/www/html/Hades/storage/app/public/citas
mkdir -p /var/www/html/Hades/storage/app/public/idoneidades
mkdir -p /var/www/html/Hades/storage/app/public/solicitudes_cambio


/* Permisos al storage*/


sudo chown -R apache:apache /var/www/html/Hades/storage/
sudo chown -R apache:apache /var/www/html/Hades/bootstrap/cache

sudo chmod -R 775 /var/www/html/Hades/storage
sudo chmod -R 775 /var/www/html/Hades/bootstrap/cache

sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/html/Hades/storage(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/html/Hades/bootstrap/cache(/.*)?"

sudo restorecon -Rv /var/www/html/Hades/storage
sudo restorecon -Rv /var/www/html/Hades/bootstrap/cache

/* para entrar a la BD */
mysql -u apolo -p

/* entra a la db */
use atlas;

/* eliminar si es necesario */
DROP DATABASE atlas;

/* ejecuta scrits guardados en carpeta database */
source /var/www/html/Hades/database/atlas_ofi_f.sql;
source /var/www/html/Hades/database/actualizaciones.sql;

/* entra a la db */
use atlas;

/* valida tablas  */
show tables; 

/* salida de bd  */
exit;


sudo chmod -R 775 /var/www/html/Hades/storage/app/public
sudo chown -R apache:apache /var/www/html/Hades/storage/app/public


php artisan storage:link

/* http - https*/
sudo nano /etc/httpd/conf.d/hades.conf
sudo nano /etc/httpd/conf.d/ssl.conf

/* copiar y pegar */
<VirtualHost *:80>

    ServerAdmin sirio.migracion.gob.pa
    DocumentRoot "/var/www/html/Hades/public"

    <Directory "/var/www/html/Hades/public">
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "/var/log/httpd/hades-error.log"
    CustomLog "/var/log/httpd/hades-access.log" combined
</VirtualHost>
/* FIN */

/* borra cache */
php artisan optimize:clear 

/* caché para producción */
php artisan config:cache 
php artisan route:cache 
php artisan view:cache 
php artisan event:cache

/* Eliminina el log si esta muy pesado*/
echo "" > storage/logs/laravel.log


/* si se hacer algun cambio en el proyecto */
cd Hades

git pull

/* MYSQL */

sudo systemctl status mysqld

sudo systemctl start mysqld

sudo systemctl restart mysqld

sudo systemctl stop mysqld

sudo systemctl enable mysqld


/* APACHE */

sudo systemctl status httpd

sudo systemctl start httpd

sudo systemctl restart httpd

sudo systemctl stop httpd

sudo systemctl enable httpd

/etc/httpd/conf.d/ssl.conf

/* instalar ldap*/

sudo dnf install -y php-ldap

/* Ejecuta proyecto */
php -S 172.20.10.81:8080 -t public


/* Ver Errores */

tail -f /var/log/httpd/error_log
tail -f /var/log/httpd/access_log


/* Validar permisos */
ls -l /var/www/html/Apolo/public/.htaccess 



systemctl restart php-fpm | systemctl restart httpd