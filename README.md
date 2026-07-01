# Logic Coach V1

## Presentation

Logic Coach est une application de bien-etre developpee avec Symfony 7.4 dans le cadre de la formation Developpeur Web et Web Mobile (DWWM).

Son objectif est d'accompagner l'utilisateur au quotidien grace a un questionnaire simple portant sur son sommeil, son niveau d'energie, son stress, sa motivation et son humeur.

A partir des reponses fournies, l'application calcule un score sur 50, determine un etat, puis propose automatiquement un message et un conseil adaptes.

Logic Coach n'a pas pour objectif de juger, d'etablir un diagnostic ou de prendre des decisions a la place de l'utilisateur. Son role est d'accompagner l'utilisateur avec bienveillance et de l'aider a prendre du recul sur sa journee.

Ce projet est concu pour rester simple, propre et facilement defendable devant un jury DWWM.

## Technologies

- Symfony 7.4 LTS
- PHP 8.3
- Doctrine ORM
- MySQL
- Twig
- PHPUnit
- Git
- GitHub

## Fonctionnalites principales

- Authentification utilisateur avec Symfony Security.
- Tableau de bord utilisateur.
- Gestion des entites User, Theme, DailyEntry et Message.
- CRUD Symfony pour les principales entites.
- Questionnaire quotidien DailyEntry.
- Association automatique du DailyEntry a l'utilisateur connecte.
- Calcul automatique du score quotidien sur 50.
- Determination automatique de l'etat du jour.
- Generation automatique d'un message et d'un conseil adaptes.
- Choix du theme utilisateur depuis une liste de themes existants.

## Logique DailyEntry

Le formulaire quotidien demande :

- les heures de sommeil ;
- le niveau d'energie ;
- le niveau de stress ;
- la motivation ;
- l'humeur.

Le sommeil est converti en note :

- moins de 4h : 0
- de 4h a moins de 5h : 4
- de 5h a moins de 6h : 5
- de 6h a moins de 7h : 6
- de 7h a moins de 8h : 8
- de 8h a 9h : 10
- plus de 9h : 9

Le score est calcule sur 50 :

```text
Sommeil + Energie + Motivation + Humeur + (10 - Stress)
```

L'etat est determine selon le score :

- 40 a 50 : excellent
- 30 a 39 : good
- 20 a 29 : average
- 10 a 19 : difficult
- 0 a 9 : critical

Chaque etat genere automatiquement un message et un conseil.

## Installation

```bash
git clone https://github.com/blinky-pink/Logic-Coach-V1.git

cd Logic-Coach-V1

composer install

php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate

symfony server:start
```

## Licence

Projet pedagogique realise dans le cadre de la formation DWWM.
