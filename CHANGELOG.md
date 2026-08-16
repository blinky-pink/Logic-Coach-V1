# CHANGELOG

Toutes les évolutions importantes du projet Logic Coach V1 sont documentées dans ce fichier.

---

## Version 0.1.0 - 28/06/2026

### Ajout

- Initialisation du projet Symfony 7.4 LTS.
- Création de l'entité User.
- Création de l'entité Theme.
- Création de l'entité DailyEntry.
- Création de l'entité Message.
- Génération des CRUD Symfony.
- Mise en place de l'authentification.
- Hachage des mots de passe.
- Configuration de la sécurité Symfony.
- Protection des routes.
- Validation du schéma Doctrine.
- Premiers tests fonctionnels.

### Corrigé

- Correction des relations entre User et Message.
- Correction du formulaire User.
- Conservation du mot de passe lors de la modification d'un utilisateur.
- Suppression de l'affichage du hash des mots de passe dans le CRUD User.

---

## 29/06/2026

### Landing Page

- Création de la page d'accueil.
- Ajout du bouton d'accès.
- Mise en place du thème sombre.

### Documentation

- Création du README.
- Création de la roadmap.

---

## 01/07/2026

### DailyEntry

- Association automatique d'une saisie quotidienne à l'utilisateur connecté.
- Calcul automatique de la note de sommeil.
- Calcul automatique du score quotidien sur 50.
- Prise en compte du stress avec la formule `10 - stress`.
- Détermination automatique de l'état du jour.
- Génération automatique d'un message.
- Génération automatique d'un conseil personnalisé.
- Affichage de l'état dans les pages de liste et de détail.

### Theme

- Ajout du choix du thème pour l'utilisateur connecté.
- Affichage du thème actuel dans la liste des thèmes.
- Ajout d'un accès au choix du thème depuis le tableau de bord.

### Documentation

- Mise à jour du README.
- Mise à jour du CHANGELOG.
- Mise à jour de la ROADMAP.