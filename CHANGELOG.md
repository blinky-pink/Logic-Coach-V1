# CHANGELOG

Toutes les évolutions importantes du projet Logic Coach V1 sont documentées dans ce fichier.

---

## Version 0.1.0 - 28/06/2026

### Ajout

* Initialisation du projet Symfony 7.4 LTS.
* Création de l'entité User.
* Création de l'entité Theme.
* Création de l'entité DailyEntry.
* Création de l'entité Message.
* Génération des CRUD Symfony.
* Mise en place de l'authentification.
* Hachage des mots de passe.
* Configuration de la sécurité Symfony.
* Protection des routes.
* Validation du schéma Doctrine.
* Premiers tests fonctionnels.

### Corrigé

* Correction des relations entre User et Message.
* Correction du formulaire User.
* Conservation du mot de passe lors de la modification d'un utilisateur.
* Suppression de l'affichage du hash des mots de passe dans le CRUD User.
