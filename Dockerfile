FROM php:8.1-cli

# Copia los archivos del proyecto al contenedor
COPY . /var/www/html

# Cambia al directorio del proyecto
WORKDIR /var/www/html

# Expone el puerto 80
EXPOSE 80

# Usa PHP como servidor al ejecutar el contenedor
CMD ["php", "-S", "0.0.0.0:80"]
