-- =====================================================================
-- Interminale — correctif d'authentification MySQL 8.0 pour PHP 5.6
-- =====================================================================
-- Depuis MySQL 8.0, le plugin d'authentification par défaut est
-- caching_sha2_password. Le pilote mysqlnd embarqué dans PHP 5.6 (la
-- version utilisée par ce projet de 2016, voir Dockerfile) ne sait pas
-- dialoguer avec ce plugin : toute connexion PDO échouerait avec une
-- erreur "The server requested authentication method unknown to the
-- client".
--
-- On force donc explicitement le compte applicatif "interminale" (créé
-- par l'entrypoint officiel de l'image MySQL à partir des variables
-- MYSQL_USER/MYSQL_PASSWORD du docker-compose.yml, AVANT l'exécution
-- des scripts de /docker-entrypoint-initdb.d/) à utiliser le plugin
-- historique mysql_native_password, pleinement compatible avec
-- mysqlnd/PHP 5.6 — sans toucher au plugin par défaut du serveur ni à
-- l'authentification du compte root.
--
-- Préfixé "00-" pour s'exécuter avant schema.sql (ordre alphabétique
-- des scripts d'init de l'image officielle MySQL).
-- =====================================================================

ALTER USER 'interminale'@'%' IDENTIFIED WITH mysql_native_password BY 'interminale';
FLUSH PRIVILEGES;
