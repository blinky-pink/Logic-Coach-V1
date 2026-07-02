# Logic Coach V1

 **Projet développé en suivant les bonnes pratiques Symfony : séparation des responsabilités, logique métier centralisée dans un service, tests unitaires avec PHPUnit et gestion de versions avec Git.**

## Présentation

Logic Coach est une application de bien-être développée avec **Symfony 7.4 LTS** dans le cadre de la formation **Développeur Web et Web Mobile (DWWM)**.

Son objectif est d'accompagner quotidiennement l'utilisateur grâce à un questionnaire portant sur :

- Sommeil
- Énergie
- Stress
- Motivation
- Humeur

À partir des réponses, l'application applique des règles métier afin de :

- calculer un score sur 50 ;
- déterminer l'état de la journée ;
- générer automatiquement un message personnalisé ;
- proposer un conseil adapté.

Logic Coach ne remplace pas un professionnel de santé. Il s'agit d'un outil pédagogique d'accompagnement permettant à l'utilisateur de prendre du recul sur son bien-être quotidien.

---

# Technologies

- Symfony 7.4 LTS
- PHP 8.3
- Doctrine ORM
- MySQL
- Twig
- PHPUnit 12
- Git
- GitHub

---

# Architecture

Le projet respecte les bonnes pratiques Symfony.

```
src/
├── Controller/
├── Entity/
├── Form/
├── Repository/
├── Security/
└── Service/
    └── BusinessRulesService.php
```

La logique métier est centralisée dans **BusinessRulesService**, ce qui permet :

- un contrôleur léger ;
- une meilleure maintenabilité ;
- des tests unitaires dédiés ;
- une architecture évolutive.

---

# Fonctionnalités

## Authentification

- Inscription
- Connexion
- Déconnexion
- Sécurisation des routes

## Tableau de bord

- Accueil utilisateur
- Navigation

## DailyEntry

Chaque saisie quotidienne permet de renseigner :

- heures de sommeil ;
- énergie ;
- stress ;
- motivation ;
- humeur.

Le système calcule automatiquement :

- le score ;
- l'état ;
- le message ;
- le conseil.

## Messagerie privée

- Envoi d'un message
- Réception d'un message
- Historique

## Thèmes

L'utilisateur peut sélectionner un thème graphique.

---

# Règles métier

Le sommeil est converti en score :

| Sommeil | Score |
|---------|------:|
| < 4 h | 0 |
| 4 h | 4 |
| 5 h | 5 |
| 6 h | 6 |
| 7 h | 8 |
| 8 à 9 h | 10 |
| > 9 h | 9 |

Le score est calculé selon la formule :

```
Sommeil
+ Énergie
+ Motivation
+ Humeur
+ (10 - Stress)
```

Le score détermine cinq états :

| Score | État |
|-------:|------|
| 40 - 50 | excellent |
| 30 - 39 | good |
| 20 - 29 | average |
| 10 - 19 | difficult |
| 0 - 9 | critical |

Chaque état génère automatiquement :

- un message personnalisé ;
- un conseil adapté.

---

# Tests

Le projet est couvert par plusieurs tests unitaires.

## Tests réalisés

- ✅ UserTest
- ✅ ThemeTest
- ✅ MessageTest
- ✅ DailyEntryTest
- ✅ BusinessRulesServiceTest

Les règles métier sont testées à l'aide de **PHPUnit Data Providers** afin de vérifier les cinq scénarios fonctionnels de Logic Coach.

---

# Installation

```bash
git clone https://github.com/blinky-pink/Logic-Coach-V1.git

cd Logic-Coach-V1

composer install

php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate

symfony server:start
```

---

# État du projet

Version actuelle :

**Version 1.1**

Fonctionnalités principales terminées.

Architecture Symfony respectée.

Tests unitaires validés.

Projet en cours de finalisation (tests fonctionnels et préparation de la soutenance).

---

# Auteur

**Philippe Olivier**

Projet réalisé dans le cadre de la formation **Développeur Web et Web Mobile (DWWM)**.

---

# Licence

Projet pédagogique.