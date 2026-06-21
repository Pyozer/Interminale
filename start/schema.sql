-- =====================================================================
-- Interminale — Reconstruction du schéma SQL (projet PHP/MySQL, ~2016)
-- =====================================================================
-- Ce schéma a été déduit à 100% par analyse statique du code source PHP
-- (requêtes PDO préparées trouvées dans app/class/*.class.php,
-- app/ajax/*.php, app/controllers/*.php et les fichiers racine *.php).
-- Aucun dump SQL d'origine n'était disponible.
--
-- Conventions observées dans le code d'origine et reproduites ici :
--   - Les booléens "logiques" (notifMailPrive, allowFindSearch, comptePrive)
--     sont stockés comme la CHAINE 'true' / 'false' (comparaison ===
--     "true" dans le PHP), pas comme un TINYINT. On respecte ce choix
--     (discutable) pour ne rien casser.
--   - Les dates "techniques" (date_post, date_comment, date_msg,
--     last_ping, date_fichier) sont des timestamps UNIX (time() en PHP),
--     stockées en INT.
--   - users.lastco est généré via strftime('%d %B %Y à %H:%M', ...),
--     donc une CHAINE FRANÇAISE formatée, pas une vraie colonne DATE/DATETIME.
--   - users.dateinscription et users.datedenaissance sont de vraies
--     dates SQL (format Y-m-d).
--   - evenements.date_event est une vraie DATETIME (utilisée avec
--     BETWEEN et DATE_FORMAT()).
--   - Les "FK" applicatives entre posts/comments/messages/amis/fichier
--     et users se font via le pseudo (VARCHAR), pas via users.id.
--     On ajoute donc des INDEX (et pas de contrainte FOREIGN KEY stricte)
--     sur ces colonnes pour rester fidèle au fonctionnement réel de
--     l'appli, sauf pour comments.id_post -> posts.id qui est, lui,
--     une vraie relation entière fiable.
--   - Jeu de caractères utf8 (le code fait "SET CHARACTER SET utf8"
--     en PDO) — vous pouvez migrer vers utf8mb4 sans risque si besoin.
-- =====================================================================

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Table : users
-- Source : app/class/user.class.php, profil.class.php, img.class.php,
--          include/init.php, inscription.php, parametres.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `pseudo`           VARCHAR(100)   NOT NULL COMMENT 'identifiant unique type prenom.nom, utilisé dans les URLs',
  `prenom`           VARCHAR(50)    NOT NULL,
  `nom`              VARCHAR(50)    NOT NULL,
  `prenomplusnom`    VARCHAR(101)   NOT NULL COMMENT 'concat prenom+" "+nom, utilisé pour la recherche',
  `email`            VARCHAR(150)   NOT NULL,
  `password`         VARCHAR(255)   NOT NULL COMMENT 'bcrypt via password_hash()',
  `classe`           VARCHAR(20)    NOT NULL COMMENT 'terminale-s | terminale-es | terminale-l',
  `datedenaissance`  DATE           DEFAULT NULL,
  `sexe`              VARCHAR(10)    DEFAULT NULL COMMENT 'Homme | Femme',
  `dateinscription`  DATE           DEFAULT NULL,
  `notifMailPrive`   VARCHAR(5)     NOT NULL DEFAULT 'true' COMMENT 'string "true"/"false"',
  `allowFindSearch`  VARCHAR(5)     NOT NULL DEFAULT 'true' COMMENT 'string "true"/"false"',
  `comptePrive`      VARCHAR(5)     NOT NULL DEFAULT 'false' COMMENT 'string "true"/"false"',
  `lastco`           VARCHAR(60)    DEFAULT NULL COMMENT 'date formatée via strftime(), texte libre',
  `session`          VARCHAR(32)    DEFAULT NULL COMMENT 'md5(rand()) régénéré à chaque login',
  `bio`              VARCHAR(255)   DEFAULT NULL COMMENT '175 caractères max appliqué côté PHP',
  `imageprofil`      VARCHAR(255)   DEFAULT NULL COMMENT 'nom de fichier dans /images/profil/',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_pseudo` (`pseudo`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_prenomplusnom` (`prenomplusnom`),
  KEY `idx_users_classe` (`classe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : forget_password
-- Source : app/class/user.class.php (forget_password), forget_password.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `forget_password` (
  `id`     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `email`  VARCHAR(150)  NOT NULL,
  `token`  VARCHAR(64)   NOT NULL COMMENT 'sha1(uniqid().email)',
  PRIMARY KEY (`id`),
  KEY `idx_forget_password_email_token` (`email`, `token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : connected_user
-- Source : app/class/user.class.php (valid_user_connected, user_connected,
--          all_user_connected, remove_user_connected)
-- Sert de "présence" temps quasi-réel (ping toutes les ~30s côté JS).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `connected_user` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `id_user`    INT UNSIGNED  NOT NULL,
  `last_ping`  INT UNSIGNED  NOT NULL COMMENT 'timestamp UNIX (time())',
  PRIMARY KEY (`id`),
  KEY `idx_connected_user_iduser` (`id_user`),
  KEY `idx_connected_user_lastping` (`last_ping`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : amis
-- Source : app/class/friends.class.php
-- active = 0 -> demande en attente / active = 1 -> amis confirmés
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `amis` (
  `id_invitation`  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `pseudo_exp`     VARCHAR(100)  NOT NULL COMMENT 'demandeur',
  `pseudo_dest`    VARCHAR(100)  NOT NULL COMMENT 'destinataire',
  `active`         TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_invitation`),
  KEY `idx_amis_pseudo_exp` (`pseudo_exp`),
  KEY `idx_amis_pseudo_dest` (`pseudo_dest`),
  KEY `idx_amis_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : posts
-- Source : app/class/posts.class.php, app/class/like.class.php
-- likes = liste d'ID utilisateurs séparés par des virgules (legacy)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `posts` (
  `id`         INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `auteur`     VARCHAR(100)   NOT NULL COMMENT 'pseudo de l auteur',
  `content`    TEXT           NOT NULL,
  `date_post`  INT UNSIGNED   NOT NULL COMMENT 'timestamp UNIX (time())',
  `public`     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT '0=amis/classe, 1=public',
  `img_post`   VARCHAR(255)   DEFAULT NULL,
  `likes`      TEXT           DEFAULT NULL COMMENT 'CSV d id utilisateurs, ex: "3,12,45,"',
  `comments`   INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT 'colonne lue par POST::get_all_posts() mais non maintenue ailleurs (le compteur réel vient de COUNT(comments)) — conservée pour compat',
  PRIMARY KEY (`id`),
  KEY `idx_posts_auteur` (`auteur`),
  KEY `idx_posts_public` (`public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : comments
-- Source : app/class/comment.class.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `id_post`        INT UNSIGNED  NOT NULL,
  `auteur`         VARCHAR(100)  NOT NULL,
  `content`        TEXT          NOT NULL,
  `date_comment`   INT UNSIGNED  NOT NULL COMMENT 'timestamp UNIX (time())',
  PRIMARY KEY (`id`),
  KEY `idx_comments_auteur` (`auteur`),
  CONSTRAINT `fk_comments_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : messages (messagerie privée)
-- Source : app/class/messagerie.class.php, app/controllers/messagerie*.php
-- id_conv regroupe les messages d'une même conversation (incrémenté
-- manuellement via MAX(id_conv)+1, pas de table "conversations" dédiée).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `id_conv`       INT UNSIGNED  NOT NULL,
  `pseudo_dest`   VARCHAR(100)  NOT NULL,
  `pseudo_exp`    VARCHAR(100)  NOT NULL,
  `message`       TEXT          NOT NULL,
  `date_msg`      INT UNSIGNED  NOT NULL COMMENT 'timestamp UNIX (time())',
  `lu`            TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_messages_idconv` (`id_conv`),
  KEY `idx_messages_dest` (`pseudo_dest`),
  KEY `idx_messages_exp` (`pseudo_exp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : chat (chat public, instantané)
-- Source : app/ajax/chat-request.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `chat` (
  `id`        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `message`   TEXT          NOT NULL,
  `auteur`    VARCHAR(100)  NOT NULL,
  `date_msg`  INT UNSIGNED  NOT NULL COMMENT 'timestamp UNIX (time())',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : fichier (partage de fichiers)
-- Source : app/class/fichier.class.php, partage_fichiers.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fichier` (
  `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `nom`           VARCHAR(255)   NOT NULL COMMENT 'nom de fichier physique dans /uploads/sharefiles/',
  `description`   VARCHAR(255)   NOT NULL COMMENT '55 caractères max appliqué côté PHP',
  `auteur`        VARCHAR(100)   NOT NULL,
  `date_fichier`  INT UNSIGNED   NOT NULL COMMENT 'timestamp UNIX (time())',
  `public`        TINYINT(1)     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_fichier_auteur` (`auteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------
-- Table : evenements (DS / DM / autres, par classe)
-- Source : app/class/classe.class.php (addevent), classe.php,
--          app/controllers/notifs-home.php, app/ajax/events-request.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `evenements` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name_event`   VARCHAR(60)   NOT NULL,
  `desc_event`   VARCHAR(150)  DEFAULT NULL,
  `date_event`   DATETIME      NOT NULL,
  `type_event`   VARCHAR(10)   NOT NULL COMMENT 'DS | DM | OTHER',
  `classe`       VARCHAR(20)   NOT NULL,
  `public`       TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_evenements_date` (`date_event`),
  KEY `idx_evenements_classe` (`classe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- Données de test
-- =====================================================================
-- Il n'existe AUCUN concept d'« admin » dans le code analysé (pas de
-- colonne role/is_admin, pas de back-office). app/admin/id_bdd.php et
-- app/admin/analyticstracking.php sont de simples fichiers de
-- configuration, pas un panneau d'administration. On insère donc
-- simplement deux comptes utilisateurs de test, ce qui correspond au
-- seul mécanisme de compte réellement présent dans l'application.
--
-- Le mot de passe des deux comptes est : Interminale2016!
-- (hash bcrypt généré hors-ligne avec un coût 9, identique à la
--  fonction passwordhash() de app/class/other.class.php, donc
--  compatible avec password_verify() utilisé par USER::login()).
-- =====================================================================

INSERT INTO `users`
  (`pseudo`, `prenom`, `nom`, `prenomplusnom`, `email`, `password`, `classe`, `datedenaissance`, `sexe`, `dateinscription`, `notifMailPrive`, `allowFindSearch`, `comptePrive`, `lastco`)
VALUES
  ('jean.dupont', 'Jean', 'Dupont', 'Jean Dupont', 'jean.dupont@interminale.local', '$2y$09$cnsM91WIFwY19mJJIL1Vx.d1LlSpDL0bTIREWXUHW3uyHCiaic7Ia', 'terminale-s', '1998-05-12', 'Homme', CURDATE(), 'true', 'true', 'false', ''),
  ('marie.martin', 'Marie', 'Martin', 'Marie Martin', 'marie.martin@interminale.local', '$2y$09$cnsM91WIFwY19mJJIL1Vx.d1LlSpDL0bTIREWXUHW3uyHCiaic7Ia', 'terminale-s', '1998-09-23', 'Femme', CURDATE(), 'true', 'true', 'false', '');

-- Une amitié confirmée entre les deux comptes de test
INSERT INTO `amis` (`pseudo_exp`, `pseudo_dest`, `active`)
VALUES ('jean.dupont', 'marie.martin', 1);

-- Une publication publique de test
INSERT INTO `posts` (`auteur`, `content`, `date_post`, `public`, `img_post`)
VALUES ('jean.dupont', 'Salut tout le monde, premier post de test ! :)', UNIX_TIMESTAMP(), 1, '');

-- Un évènement de test (DS d'anglais, visible par la classe terminale-s)
INSERT INTO `evenements` (`name_event`, `desc_event`, `date_event`, `type_event`, `classe`, `public`)
VALUES ('Anglais', 'Chapitres 1 à 3, prévoir une feuille double.', DATE_ADD(NOW(), INTERVAL 7 DAY), 'DS', 'terminale-s', 0);
