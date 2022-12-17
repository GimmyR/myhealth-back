# MyHealth (Back-End)

MyHealth est une application web qui permet de surveiller l'état de santé d'un individu selon des paramètres bien précis.

## Prérequis

* **PHP** version **8.1.13** (ou plus)
* **Composer** version **2.4.4** (ou plus)
* **Symfony** version **5.4.19** (ou plus)
* **MySQL** ou **MariaDB**

## Configuration de la base de données

Exécutez toutes les commandes du fichier **mysql-schema.sql** du répertoire **sql** dans votre SGBD.

## Installation des dépendances

Placez-vous dans le dossier du projet et exécutez la commande `composer install`.

## Configuration de l'application

* Dans le fichier **.env** :

Dans la section *doctrine/doctrine-bundle*, remplacez *db_user*, *db_password*, *db_hostname* et *db_port* respectivement par votre nom d'utilisateur et votre mots de passe sur votre SGBD ainsi que par l'hôte (ex: 127.0.0.1) et le port de la base.

Si vous n'avez pas un serveur SMTP à vous, dans la section *symfony/sendinblue-mailer*, j'utilise **Sendinblue** pour gérer les emails. Inscrivez-vous gratuitement sur leur site et renseignez-vous sur les paramètres SMTP de votre compte pour remplacer *smtp_user* par votre identifiant, *smtp_password* par votre mot de passe, *smtp_server* par le serveur SMTP et *smtp_port* par le port du serveur.

Si vous avez un serveur SMTP à vous, commentez ma ligne de configuration avec sendinblue et décommentez la ligne de configuration de la section *symfony/mailer*. N'oubliez pas de la compléter avec vos coordonnées.

* Dans le fichier **services.yaml** du répertoire **config** :

Remplacez la valeur du paramètre *app.front_url* par l'URL de l'application front-end (le / vers la fin est important).

Remplacez la valeur du paramètre *app.email_from* par votre identifiant **Sendinblue** si vous utilisez ce dernier.

## Lancement du serveur

Exécutez la commande `symfony server:start`.

## Front-End du projet

Accédez au front-end de l'application par [ici](https://github.com/GimmyR/myhealth-front).