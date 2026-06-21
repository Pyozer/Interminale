# Interminale — Reconstruction locale (schéma SQL + Docker)

Ce dossier `start/` contient tout ce qu'il faut pour faire retourner
localement ce projet PHP/MySQL de 2016, dont la base de données
d'origine a été perdue.

## Ce qui a été fait

1. **Analyse intégrale du code source** : toutes les requêtes SQL
   (PDO préparées) trouvées dans `app/class/*.class.php`,
   `app/ajax/*.php`, `app/controllers/*.php`, `include/init.php` et les
   fichiers racine `*.php` ont été passées en revue pour identifier
   les tables, colonnes, types de données probables et relations.
2. **`schema.sql`** : reconstruction de **11 tables**
   (`users`, `forget_password`, `connected_user`, `amis`, `posts`,
   `comments`, `messages`, `chat`, `fichier`, `evenements`), avec deux
   comptes de test et quelques données de démo.
3. **Détection de version PHP** : le fichier nginx d'origine
   (`default`, à la racine) pointe explicitement vers
   `unix:/var/run/php5-fpm.sock`, et le code utilise
   `password_hash()`/`password_verify()` (PHP 5.5+) ainsi que la
   syntaxe `[...]` (PHP 5.4+), sans aucune fonctionnalité propre à
   PHP 7. Conclusion : **PHP 5.6** (dernière branche 5.x, cohérente
   avec un projet daté 2016).
4. **`Dockerfile` + `docker-compose.yml`** : environnement local complet
   (Apache/PHP 5.6 + MySQL 8.0 + Node.js/Socket.IO), avec montage
   automatique du schéma et montage du code source parent dans le
   conteneur web.

## Important : il n'y a pas de compte "admin"

Le code analysé ne contient **aucun** concept d'administrateur (pas de
colonne `role`/`is_admin`, pas de back-office). `app/admin/id_bdd.php`
et `app/admin/analyticstracking.php` sont de simples fichiers de
configuration, pas un panneau d'admin. Les données de test insérées
sont donc deux comptes utilisateurs classiques.

## Démarrer le projet

Depuis le dossier `start/` :

```bash
docker compose up -d --build
```

Puis ouvrez **http://localhost:8080/home**

Le serveur de chat temps réel (Socket.IO) démarre automatiquement avec
les autres services et écoute sur **http://localhost:3000** : le chat
et les notifications temps réel (messages privés, demandes d'amis)
fonctionnent donc en live, comme dans l'application d'origine.

> Les URLs "jolies" (`/home`, `/connexion`, `/profil/...`, etc.) sont
> gérées par des règles Apache `RewriteRule` dans `000-default.conf`,
> qui reproduisent les règles `rewrite ... last;` du fichier nginx
> d'origine du projet.

## Comptes de test

| Email                              | Mot de passe        | Classe        |
|-------------------------------------|----------------------|---------------|
| jean.dupont@interminale.local       | `Interminale2016!`  | Terminale S   |
| marie.martin@interminale.local      | `Interminale2016!`  | Terminale S   |

Ces deux comptes sont déjà amis (table `amis`), et `jean.dupont` a
publié un post public de test.

## Fichiers de ce dossier

| Fichier                  | Rôle |
|----------------------------|------|
| `schema.sql`               | DDL complet + données de test, monté automatiquement dans MySQL via `docker-entrypoint-initdb.d/` |
| `00-auth-fix.sql`           | Filet de sécurité : repasse explicitement le compte applicatif `interminale` sur le plugin `mysql_native_password` si jamais il n'avait pas été créé ainsi par l'option `--default-authentication-plugin` du service `db` (voir `docker-compose.yml`). Monté dans `docker-entrypoint-initdb.d/`, exécuté avant `schema.sql`. |
| `Dockerfile`                | Image Apache + PHP 5.6 avec les extensions `pdo_mysql`, `mysqli`, `mbstring`, et la config php.ini/Apache |
| `docker-compose.yml`        | Orchestration des services `db` (MySQL 8.0), `web` (Apache/PHP) et `nodejs` (Socket.IO) |
| `000-default.conf`          | VirtualHost Apache : `DocumentRoot /var/www` + règles de réécriture d'URL |
| `php-overrides.ini`         | Réglages php.ini absents de l'image Docker officielle mais présents sur le serveur d'origine (voir "Dépannage" ci-dessous) |
| `id_bdd.docker.php`         | **Surcharge** (montée par-dessus `app/admin/id_bdd.php` au runtime) pointant vers le service MySQL `db` au lieu de `localhost`. Le fichier source original **n'est pas modifié**. |
| `client.docker.js`          | **Surcharge** (montée par-dessus `app/nodejs/client.js` au runtime) pointant le client Socket.IO vers `http://localhost:3000` au lieu du domaine de prod historique (`www.interminale.fr.nf:3000`, hors service). Le fichier source original **n'est pas modifié**. Seule la ligne `io.connect(...)` diffère du fichier d'origine. |

## Le service `nodejs` (Socket.IO)

Le service `nodejs` du `docker-compose.yml` fait tourner tel quel
`app/nodejs/server.js` (serveur Socket.IO 1.3.7 d'origine, non modifié)
avec l'image officielle `node:8-alpine`. Aucune étape de build n'est
nécessaire : les dépendances (`socket.io` et ses sous-dépendances)
étaient déjà vendorisées dans `app/nodejs/node_modules/` (commitées
dans le dépôt, pratique courante en 2016), donc le dossier est monté
tel quel et `node server.js` est exécuté directement.

Seul le **client** (`app/nodejs/client.js`, chargé par
`include/footer.php`) doit être adapté pour fonctionner en local : il
pointe en dur vers `http://www.interminale.fr.nf:3000`, le domaine de
production historique, aujourd'hui hors service. Comme pour
`id_bdd.docker.php`, ce n'est pas le fichier source qui est modifié,
mais une **surcharge montée par-dessus** (`client.docker.js`, voir
tableau ci-dessus) qui ne change que l'URL de connexion
(`http://localhost:3000`).

## Pourquoi MySQL 8.0 (et pas 5.7) ?

La version initiale de cet environnement utilisait MySQL 5.7, dont le
plugin d'authentification par défaut (`mysql_native_password`) est
nativement compatible avec le pilote `mysqlnd` de PHP 5.6. Problème :
Oracle n'a **jamais publié de build `linux/arm64` pour `mysql:5.7`**,
ce qui fait échouer `docker compose up` sur Mac Apple Silicon
(puces M1/M2/M3/M4) avec une erreur du type :

```
Image mysql:5.7   Error no matching manifest for linux/arm64/v8 in the manifest list entries: no match for platform in manifest: not found
```

`mysql:8.0`, en revanche, dispose bien d'un build `arm64v8` officiel.
On utilise donc MySQL 8.0, mais en restaurant trois comportements de
l'ancienne version 5.7 indispensables à PHP 5.6, tous configurés sur le
service `db` de `docker-compose.yml` :

- le charset/collation par défaut du serveur (`utf8`/`utf8_general_ci`
  au lieu de `utf8mb4`/`utf8mb4_0900_ai_ci`), via les options
  `--character-set-server` / `--collation-server` ;
- le plugin d'authentification par défaut (`mysql_native_password` au
  lieu de `caching_sha2_password`), via l'option
  `--default-authentication-plugin`, qui s'applique dès la création
  des comptes `root` et `interminale` ;
- en filet de sécurité, `00-auth-fix.sql` repasse explicitement le
  compte `interminale` sur ce même plugin (voir tableau ci-dessus).

Sans ça, `mysqlnd` (le pilote MySQL de PHP 5.6) ne sait dialoguer ni
avec le nouveau plugin d'authentification, ni avec la nouvelle
collation par défaut introduite en MySQL 8.0.

> ⚠️ Contrairement au charset (appliqué à chaque démarrage du
> serveur), le plugin d'authentification n'est fixé qu'à la **création**
> d'un compte. Si vous avez déjà un volume `interminale_db_data`
> initialisé avant l'ajout de `--default-authentication-plugin`, un
> simple redémarrage ne suffit pas : voir le dépannage ci-dessous.

> Si vous êtes sur un Mac Intel ou un PC Windows/Linux classique,
> `mysql:8.0` fonctionne aussi sans aucune adaptation.

## Pourquoi `DocumentRoot /var/www` (et pas `/var/www/html`) ?

Les classes `app/class/img.class.php` et `app/class/fichier.class.php`
écrivent les fichiers uploadés dans un chemin **codé en dur** :
`define('TARGET', '/var/www'.$dir)`, indépendamment de
`$_SERVER['DOCUMENT_ROOT']`. Pour que ces fichiers uploadés soient
ensuite correctement servis (le code vérifie leur existence via
`$_SERVER['DOCUMENT_ROOT'].$imghref`), le `DocumentRoot` Apache doit
être strictement `/var/www`, exactement comme dans le `root /var/www;`
du nginx d'origine.

## Dépannage (problèmes déjà rencontrés et corrigés)

Ces problèmes ont été corrigés **uniquement côté infrastructure
Docker/Apache/PHP** — aucun fichier du code source du projet
(`app/`, `include/`, `*.php` à la racine, etc.) n'a été modifié.

### `Bad Request` sur les URLs type `/home`

Cause : dans `000-default.conf`, les cibles de `RewriteRule` étaient
écrites sans slash initial (`home.php` au lieu de `/home.php`). En
contexte `<VirtualHost>`, une substitution sans slash absolu génère une
URI hybride invalide, qu'Apache rejette avec un 400 avant même
d'atteindre PHP. **Corrigé** : toutes les cibles utilisent désormais un
chemin absolu (`/home.php`, `/connexion.php`, etc.).

### `Cannot modify header information - headers already sent`

Cause : l'image officielle `php:5.6-apache` ne charge **aucun**
`php.ini` par défaut, contrairement au serveur Debian/Ubuntu d'origine
du projet (paquets `php5`, dont le `php.ini` fourni par la distribution
activait `output_buffering`). Le code du projet appelle `header()` /
`setcookie()` après que du HTML a déjà été affiché (pattern classique
de `header.php` → contenu HTML → puis `redirect()` plus loin dans le
script) ; sans `output_buffering`, ça casse. **Corrigé** :
`php-overrides.ini` réactive `output_buffering = 4096`, ce qui restaure
le comportement du serveur d'origine.

> Si vous avez déjà lancé `docker compose up` avant ces correctifs,
> relancez avec `docker compose up -d --build` pour reconstruire
> l'image avec la nouvelle configuration Apache/PHP.

### `Error no matching manifest for linux/arm64/v8` (Mac Apple Silicon)

Cause : l'image `mysql:5.7` n'existe pas en `arm64`. **Corrigé** :
passage à `mysql:8.0` (qui dispose d'un build `arm64v8`), voir
"Pourquoi MySQL 8.0" ci-dessus pour le détail des correctifs de
compatibilité associés.

> Si vous aviez déjà lancé `docker compose up` avant ce correctif,
> supprimez le volume de données MySQL existant pour repartir d'une
> base vierge : `docker compose down -v && docker compose up -d --build`

### `Server sent charset (255) unknown to the client`

Cause : depuis MySQL 8.0, la collation par défaut du serveur est
`utf8mb4_0900_ai_ci` (id 255), introduite avec MySQL 8.0 et inconnue
de `mysqlnd` sous PHP 5.6. L'erreur survient dès `PDO::__construct()`
(`include/init.php` ligne 17), avant même que le `SET CHARACTER SET
utf8` du code n'ait la moindre chance de s'exécuter. **Corrigé** : le
service `db` force `--character-set-server=utf8
--collation-server=utf8_general_ci` (voir `docker-compose.yml`), pour
retrouver le comportement par défaut de MySQL 5.7.

> Cette option agit au démarrage du serveur MySQL, pas à
> l'initialisation des données : un simple
> `docker compose up -d --build` (sans `-v`) suffit à appliquer le
> correctif, même si le volume `interminale_db_data` existe déjà.

### `The server requested authentication method unknown to the client [caching_sha2_password]`

Cause : le compte applicatif `interminale` a été créé (lors de la
première initialisation du volume MySQL) avec `caching_sha2_password`,
le plugin d'authentification par défaut de MySQL 8.0, que `mysqlnd`
sous PHP 5.6 ne sait pas négocier — c'est typiquement ce qui se
produit si ce compte a été créé **avant** l'ajout de l'option
`--default-authentication-plugin` au service `db`. **Corrigé** :
`docker-compose.yml` force désormais
`--default-authentication-plugin=mysql_native_password`, avec
`00-auth-fix.sql` en filet de sécurité supplémentaire.

> ⚠️ Comme pour le problème `arm64` ci-dessus (et contrairement au
> problème de charset), ce correctif n'agit qu'à la **création** du
> compte MySQL : un simple `docker compose up -d --build` ne suffit
> **pas** s'il existe déjà un volume `interminale_db_data` initialisé
> avec l'ancienne configuration. Repartez d'une base vierge :
> `docker compose down -v && docker compose up -d --build`

## Limites connues / points d'attention

- Les emails (mot de passe oublié, demandes d'amis, messages privés)
  utilisent PHPMailer avec un compte Gmail SMTP codé en dur dans le
  code source d'origine (`app/class/user.class.php`,
  `app/ajax/friends-request.php`, `app/ajax/msgprive-request.php`).
  Ces identifiants sont très probablement révoqués/obsolètes ; ces
  fonctionnalités d'envoi de mail échoueront silencieusement en local
  (le code gère déjà l'échec d'envoi sans bloquer l'utilisateur).
