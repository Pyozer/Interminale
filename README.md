# Interminale

Mini réseau social pensé pour les élèves de Terminale : fil
d'actualité, messagerie privée, chat en temps réel, gestion de classe
et partage de fichiers entre camarades.

Projet personnel développé en PHP/MySQL en 2015-2016.

## Fonctionnalités

- **Compte utilisateur** : inscription, connexion, mot de passe oublié,
  profil personnalisable (photo, bio, paramètres de confidentialité)
- **Fil d'actualité** : publications, likes, commentaires
- **Amis** : recherche d'utilisateurs, demandes d'amis, liste d'amis
- **Messagerie privée** entre utilisateurs
- **Chat public en temps réel** (Socket.IO)
- **Gestion de classe** : calendrier des devoirs surveillés / maison (DS, DM)
- **Partage de fichiers** entre élèves

## Stack technique

- **Backend** : PHP 5.6 (PDO)
- **Base de données** : MySQL
- **Temps réel** : Node.js + Socket.IO
- **Frontend** : Bootstrap (Material Design), jQuery
- **Emails** : PHPMailer

## Lancer le projet en local

Le projet se lance entièrement avec Docker (PHP/Apache, MySQL et le
serveur Socket.IO), schéma de base de données et données de test
inclus :

```bash
cd start
docker compose up -d --build
```

Puis ouvrez **http://localhost:8080/home**.

Deux comptes de test sont disponibles dès le premier démarrage :

| Email                          | Mot de passe         |
|---------------------------------|------------------------|
| jean.dupont@interminale.local   | `Interminale2016!`    |
| marie.martin@interminale.local  | `Interminale2016!`    |

📖 Pour le détail de la configuration Docker (choix techniques,
dépannage, etc.), voir [`start/README.md`](start/README.md).
