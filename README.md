# Logic Coach V1

**Projet développé en suivant les bonnes pratiques Symfony : séparation des responsabilités, logique métier centralisée dans un service, tests automatisés avec PHPUnit et gestion de versions avec Git.**

---

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

Le projet respecte une architecture Symfony organisée autour des responsabilités principales de l'application.

```text
src/
├── Controller/
├── Entity/
├── Form/
├── Repository/
└── Service/
    └── BusinessRulesService.php
```

La logique métier est centralisée dans **BusinessRulesService**, ce qui permet :

- de garder les contrôleurs plus légers ;
- de séparer la logique métier du traitement HTTP ;
- d'améliorer la maintenabilité ;
- de faciliter les tests automatisés.

---

# Fonctionnalités

## Authentification

- Inscription
- Connexion
- Déconnexion
- Hachage des mots de passe
- Protection CSRF de l'authentification
- Sécurisation des routes

---

## Gestion des rôles

L'application utilise principalement deux rôles :

### ROLE_USER

Permet à un utilisateur authentifié d'accéder aux fonctionnalités normales de l'application.

### ROLE_ADMIN

Permet d'accéder aux fonctionnalités d'administration réservées, notamment la gestion des utilisateurs et certaines opérations protégées.

---

## Tableau de bord

Le tableau de bord permet à l'utilisateur de retrouver les informations liées à son suivi quotidien.

Il affiche notamment la dernière saisie quotidienne disponible pour l'utilisateur connecté.

---

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

Une saisie quotidienne validée n'est pas modifiable.

---

## Messagerie privée

L'application dispose également d'une messagerie permettant :

- l'envoi d'un message ;
- la réception d'un message ;
- la consultation de l'historique.

Les accès aux messages sont contrôlés afin qu'un utilisateur ne puisse pas consulter les messages appartenant uniquement à d'autres utilisateurs.

---

## Thèmes

L'utilisateur peut sélectionner un thème graphique.

Les opérations d'administration concernant les thèmes sont protégées selon les droits de l'utilisateur.

---

# Règles métier

Le sommeil est converti en score :

| Sommeil | Score |
|---|---:|
| < 4 h | 0 |
| 4 h | 4 |
| 5 h | 5 |
| 6 h | 6 |
| 7 h | 8 |
| 8 à 9 h | 10 |
| > 9 h | 9 |

Le score est calculé selon la formule :

```text
Sommeil
+ Énergie
+ Motivation
+ Humeur
+ (10 - Stress)
```

Le score détermine cinq états :

| Score | État | Signification |
|---:|---|---|
| 40 - 50 | excellent | Excellent |
| 30 - 39 | good | Bien |
| 20 - 29 | average | Moyen |
| 10 - 19 | difficult | Difficulté |
| 0 - 9 | critical | Critique |

Chaque état génère automatiquement :

- un message personnalisé ;
- un conseil adapté.

---

# Tests automatisés

Le projet dispose de tests automatisés réalisés avec **PHPUnit**.

Ils couvrent notamment :

- les entités ;
- les règles métier ;
- les contrôleurs ;
- l'authentification ;
- l'inscription ;
- les autorisations d'accès ;
- les utilisateurs ;
- les thèmes ;
- les messages ;
- les saisies quotidiennes ;
- le tableau de bord.

Les règles métier sont notamment vérifiées à l'aide de **PHPUnit Data Providers** afin de tester différents scénarios fonctionnels de Logic Coach.

## État actuel des tests

```text
77 tests
255 assertions
100 % des tests réussis
```

Pour lancer les tests :

```bash
php bin/phpunit
```

---

# Installation

## 1. Cloner le projet

```bash
git clone https://github.com/blinky-pink/Logic-Coach-V1.git
```

## 2. Entrer dans le projet

```bash
cd Logic-Coach-V1
```

## 3. Installer les dépendances PHP

```bash
composer install
```

## 4. Configurer la base de données

Créer ou compléter le fichier :

```text
.env.local
```

et configurer la variable :

```text
DATABASE_URL
```

avec les informations correspondant à l'environnement MySQL utilisé.

## 5. Créer la base de données

```bash
php bin/console doctrine:database:create
```

## 6. Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

## 7. Démarrer l'application

```bash
symfony server:start
```

---

# Vérifications techniques

Le projet a été contrôlé avec les outils Symfony et Doctrine.

Les vérifications réalisées comprennent notamment :

```bash
php bin/phpunit
php bin/console doctrine:schema:validate
php bin/console doctrine:migrations:status
php bin/console debug:router
php bin/console lint:twig templates
php bin/console lint:yaml config
php bin/console lint:container
```

État actuel :

- **77 tests / 77 réussis**
- **255 assertions**
- mapping Doctrine valide ;
- schéma de base de données synchronisé ;
- migrations Doctrine à jour ;
- templates Twig valides ;
- fichiers YAML valides ;
- conteneur de services Symfony valide.

---

# État du projet

Version actuelle :

**Version 1.1**

Les fonctionnalités principales de Logic Coach V1 sont opérationnelles.

L'architecture Symfony est en place.

La base de données et les migrations Doctrine sont synchronisées.

Les tests unitaires et fonctionnels sont validés.

Le projet est actuellement dans sa phase finale de préparation pour la soutenance DWWM.

---

# Auteur

**Philippe Olivier**

Projet réalisé dans le cadre de la formation **Développeur Web et Web Mobile (DWWM)**.

---

# Licence

Projet pédagogique.