# =====================================================================
# Interminale — image web pour reconstitution locale (projet ~2016)
# =====================================================================
# Choix de PHP 5.6 :
#   - Le fichier nginx d'origine (racine du projet, fichier "default")
#     route explicitement le PHP vers "unix:/var/run/php5-fpm.sock",
#     ce qui confirme une stack PHP 5.x (et non PHP 7) en production.
#   - Le code utilise password_hash()/password_verify() (disponibles
#     depuis PHP 5.5) et la syntaxe de tableau courte [...] (PHP 5.4+),
#     mais aucune fonctionnalité propre à PHP 7 (pas de scalar type
#     hints, pas de return types, pas de "??", etc.).
#   - PHP 5.6 est donc la version la plus probable pour un projet
#     daté de 2016 (dernière branche 5.x, très répandue à l'époque).
#
# Remarque : php:5.6-apache est une image EOL non maintenue par
# l'équipe officielle PHP ; elle reste néanmoins disponible sur
# Docker Hub pour ce type d'usage "legacy / archéologie de code".
# Si jamais elle venait à disparaître, basculez sur "php:7.0-apache"
# (la compatibilité du code avec PHP 7.0 est très probable vu ce qui
# précède).
# =====================================================================

FROM php:5.6-apache

# Extensions nécessaires :
#   - pdo_mysql : utilisé partout via new PDO('mysql:...') (include/init.php)
#   - mysqli    : présent par précaution (non utilisé directement, mais
#                 inoffensif et parfois requis par des libs tierces)
#   - mbstring  : mb_strtolower() utilisé pour les emails/pseudos
RUN docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod rewrite headers

# Configuration Apache : DocumentRoot /var/www + réécriture d'URL
# équivalente à la configuration nginx d'origine (voir 000-default.conf
# pour le détail des raisons).
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Réglages php.ini absents de l'image officielle (qui n'en charge aucun
# par défaut) mais présents sur le serveur Debian/Ubuntu d'origine du
# projet — notamment output_buffering, nécessaire pour que les appels
# header()/setcookie() tardifs (ex: user.class.php::redirect()) ne
# provoquent pas d'erreur "headers already sent". Voir php-overrides.ini
# pour le détail. Le préfixe "zz-" garantit qu'il se charge en dernier.
COPY php-overrides.ini /usr/local/etc/php/conf.d/zz-interminale-overrides.ini

WORKDIR /var/www

EXPOSE 80
