# Stubborn - Boutique E-commerce Symfony

**Auteur :** Geoffroy Julien  
**Formation :** Développeur Web 2023 à aujourd’hui  
**Lien GitHub :** https://github.com/juliengeoffroy/stubborn-shop.git

## Description

Ce projet est une boutique en ligne pour la marque Stubborn, développée avec Symfony.

## Fonctionnalités

- Affichage de sweat-shirts avec images
- Filtres par prix
- Page de détail produit
- Ajout au panier avec gestion des tailles
- Paiement via Stripe en mode test
- Gestion de stock en back-office
- Interface administrateur
- Authentification (connexion / inscription)

## Démonstration

Voir les captures d’écran dans le fichier `stubborn_project_report.pdf` ou dans les dossiers `/public/images/` ou `/docs/`.

## Technologies utilisées

- Symfony 6
- PHP 8
- MySQL
- Doctrine
- Stripe (test mode)
- Twig

## Installation

```bash
git clone https://github.com/juliengeoffroy/stubborn-shop.git
cd stubborn-shop
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
npm install
npm run dev
symfony server:start
```

## Licence

Projet à usage pédagogique uniquement.
