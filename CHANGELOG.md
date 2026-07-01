# CHANGELOG

Toutes les evolutions importantes du projet Logic Coach V1 sont documentees dans ce fichier.

---

## Version 0.1.0 - 28/06/2026

### Ajout

- Initialisation du projet Symfony 7.4 LTS.
- Creation de l'entite User.
- Creation de l'entite Theme.
- Creation de l'entite DailyEntry.
- Creation de l'entite Message.
- Generation des CRUD Symfony.
- Mise en place de l'authentification.
- Hachage des mots de passe.
- Configuration de la securite Symfony.
- Protection des routes.
- Validation du schema Doctrine.
- Premiers tests fonctionnels.

### Corrige

- Correction des relations entre User et Message.
- Correction du formulaire User.
- Conservation du mot de passe lors de la modification d'un utilisateur.
- Suppression de l'affichage du hash des mots de passe dans le CRUD User.

---

## 29/06/2026

### Landing Page

- Creation de la page d'accueil.
- Ajout du bouton d'acces.
- Mise en place du theme sombre.

### Documentation

- Creation du README.
- Creation de la roadmap.

---

## 01/07/2026

### DailyEntry

- Association automatique d'une saisie quotidienne a l'utilisateur connecte.
- Calcul automatique de la note de sommeil.
- Calcul automatique du score quotidien sur 50.
- Prise en compte du stress avec la formule `10 - stress`.
- Determination automatique de l'etat du jour.
- Generation automatique d'un message.
- Generation automatique d'un conseil personnalise.
- Affichage de l'etat dans les pages de liste et de detail.

### Theme

- Ajout du choix du theme pour l'utilisateur connecte.
- Affichage du theme actuel dans la liste des themes.
- Ajout d'un acces au choix du theme depuis le tableau de bord.

### Documentation

- Mise a jour du README.
- Mise a jour du CHANGELOG.
- Mise a jour de la ROADMAP.
