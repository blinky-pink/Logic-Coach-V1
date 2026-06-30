# SEANCE_001

**Date :** 28/06/2026

## Objectif

Mettre en place les fondations de Logic Coach V1.

---

# Travail réalisé

## Base de données

* Création de l'entité User.
* Création de l'entité Theme.
* Création de l'entité DailyEntry.
* Création de l'entité Message.
* Mise en place des relations Doctrine.
* Génération et exécution des migrations.
* Validation du schéma Doctrine.

---

## CRUD

Création des CRUD Symfony pour :

* User
* Theme
* DailyEntry
* Message

---

## Sécurité

* Mise en place de l'authentification Symfony.
* Génération de la page de connexion.
* Configuration du firewall.
* Hachage des mots de passe.
* Protection des routes.
* Déconnexion.

---

## Corrections

* Correction des relations `sender` / `receiver`.
* Correction du formulaire User.
* Conservation du mot de passe lors de la modification d'un utilisateur.
* Suppression de l'affichage du hash du mot de passe dans le CRUD.

---

## Tests réalisés

* Création d'un utilisateur.
* Modification d'un utilisateur.
* Vérification du hachage des mots de passe.
* Vérification de l'authentification.
* Vérification de la déconnexion.
* Vérification de la protection des routes.
* Validation Doctrine.

---

## État du projet

Fonctionnalité terminée et validée.

---

## Prochaine fonctionnalité

Création de la Landing Page de Logic Coach.

# SEANCE_002

**Date :** 29/06/2026

## Objectif

Créer la Landing Page de Logic Coach V1.

---

# Travail réalisé

## Interface utilisateur

* Création de la page d'accueil.
* Intégration du titre **Logic Coach**.
* Ajout du sous-titre.
* Création du bouton central d'accès à l'application.
* Intégration des icônes du bien-être.
* Mise en place du thème sombre.
* Début du responsive.

---

## Ressources

* Préparation des icônes avec fond transparent.
* Organisation des images dans `public/images/home/`.
* Création du fichier `home.css`.

---

## Documentation

* Mise à jour du `README.md`.
* Création du `CHANGELOG.md`.
* Création de la `ROADMAP.md`.

---

## Difficultés rencontrées

* Positionnement des éléments de la Landing Page.
* Ajustements du responsive.
* Organisation du CSS.

---

## État du projet

Landing Page V1 terminée.

La page reste améliorable graphiquement mais constitue une première version stable permettant de poursuivre le développement de l'application.

---

## Prochaine fonctionnalité

Création du Dashboard utilisateur.

