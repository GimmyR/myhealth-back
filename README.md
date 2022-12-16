# MyHealth API

Ce projet a été développé sous **PHP** (8.1.13), **Composer** (2.4.4) et **Symfony** (5.4.19). Il est donc impératif que vous installiez au moins ces versions-là de ces programmes pour utiliser correctement cette application.

Le projet utilise également **MySQL** ou **MariaDB** comme système de gestion de base de données donc installez-le si ce n'est pas déjà fait.

## Configuration de la base de données

Exécutez toutes les commandes du fichier *mysql-schema.sql* du répertoire *sql* dans votre SGBD.

## Installation des dépendances

Exécutez la commande `composer install`.

## Configuration de l'application

* Dans le fichier *.env* :

Dans la section *doctrine/doctrine-bundle*, remplacez *db_user*, *db_password*, *db_hostname* et *db_port* respectivement par votre nom d'utilisateur et votre mots de passe sur votre SGBD ainsi que par l'hôte (ex: 127.0.0.1) et le port de la base.

Si vous n'avez pas un serveur SMTP à vous, dans la section *symfony/sendinblue-mailer*, j'utilise **Sendinblue** pour gérer les emails. Inscrivez-vous gratuitement sur leur site et renseignez-vous sur les paramètres SMTP de votre compte pour remplacer *smtp_user* par votre identifiant, *smtp_password* par votre mot de passe, *smtp_server* par le serveur SMTP et *smtp_port* par le port du serveur.

Si vous avez un serveur SMTP à vous, commentez ma ligne de configuration avec sendinblue et décommentez la ligne de configuration de la section *symfony/mailer*. N'oubliez pas de la compléter avec vos coordonnées.

* Dans le fichier *services.yaml* du répertoire *config* :

Normalement, vous n'avez pas à modifier le paramètre *app.front_url* si vous utilisez Apache (et ses configurations de base) comme serveur pour votre application front-end.

Par contre, remplacez la valeur du paramètre *app.email_from* (soit *noreply@example.com*) par votre identifiant **Sendinblue** si vous utilisez ce dernier.

## Lancement du serveur

Exécutez la commande `symfony server:start`.