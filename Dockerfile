# Utiliser une image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Installer les dépendances système (git, unzip, zip, etc.)
RUN apt-get update && apt-get install -y git unzip zip libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer les modules Apache nécessaires
RUN a2enmod rewrite

# Copier la configuration Apache personnalisée dans le conteneur
COPY apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf

# Activer le site Apache en utilisant a2ensite
RUN a2ensite 000-default.conf

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html

# Installer Composer (récupéré depuis l’image officielle)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Assurer les permissions pour Symfony
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /var/www/html/var \
    && chmod -R 777 /var/www/html/var

# Installer les dépendances Symfony
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

# Exposer le port 80
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
