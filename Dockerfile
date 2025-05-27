FROM php:8.1-apache

# Copia los archivos del proyecto al directorio web de Apache
COPY . /var/www/html/

# Activa el módulo de reescritura (opcional pero útil para .htaccess)
RUN a2enmod rewrite

# Expone el puerto 80 (ya lo hace internamente, así que no es obligatorio)
EXPOSE 80
