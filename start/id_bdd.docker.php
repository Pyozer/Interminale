<?php
/**
 * Surcharge de connexion DB pour l'environnement Docker local.
 *
 * Ce fichier N'EST PAS le fichier d'origine du projet
 * (app/admin/id_bdd.php, qui contient "localhost" en dur + les
 * identifiants de prod historiques). Il est monté PAR-DESSUS celui-ci
 * au démarrage du conteneur web (voir docker-compose.yml), uniquement
 * dans ce conteneur, ce qui permet de pointer vers le service MySQL
 * "db" du docker-compose SANS modifier le code source du projet.
 *
 * Les valeurs ci-dessous doivent rester synchronisées avec les
 * variables d'environnement MYSQL_* définies dans docker-compose.yml.
 */
$DB_host = "db";
$DB_user = "interminale";
$DB_pass = "interminale";
$DB_name = "interminale";
?>
