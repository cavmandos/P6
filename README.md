# Projet 6 : SnowTricks

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/ed55232d6e554a24beeabe58b1bde2ee)](https://app.codacy.com/gh/cavmandos/P6/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

Un site communautaire pour les passionnés de Snowboard.

## Table des matières

-  [Aperçu](#aperçu)
-  [Description](#description)
-  [Installation](#installation)

## Aperçu

Ce projet est effectué dans le cadre du parcours PHP/Symfony d'Openclassroom.

Jimmy Sweat est un entrepreneur ambitieux passionné de snowboard. Son objectif est la création d'un site collaboratif pour faire connaître ce sport auprès du grand public et aider à l'apprentissage des figures (tricks).

Il souhaite capitaliser sur du contenu apporté par les internautes afin de développer un contenu riche et suscitant l’intérêt des utilisateurs du site. Par la suite, Jimmy souhaite développer un business de mise en relation avec les marques de snowboard grâce au trafic que le contenu aura généré.

Pour ce projet, nous allons nous concentrer sur la création technique du site pour Jimmy.

## Description

Vous êtes chargé de développer le site répondant aux besoins de Jimmy. Vous devez ainsi implémenter les fonctionnalités suivantes : 

-  un annuaire des figures de snowboard. Vous pouvez vous inspirer de la liste des figures sur Wikipédia. Contentez-vous d'intégrer 10 figures, le reste sera saisi par les internautes ;
-  la gestion des figures (création, modification, consultation) ;
-  un espace de discussion commun à toutes les figures.

Pour implémenter ces fonctionnalités, vous devez créer les pages suivantes :

-  la page d’accueil où figurera la liste des figures ; 
-  la page de création d'une nouvelle figure ;
-  la page de modification d'une figure ;
-  la page de présentation d’une figure (contenant l’espace de discussion commun autour d’une figure).

## Installation

Pour exécuter ce projet localement, suivez ces étapes simples :

1.  Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre système :

-  [PHP](https://www.php.net/manual/en/install.php)
-  [Composer](https://getcomposer.org/download/)
-  [Symfony CLI](https://symfony.com/download)

2.  Clonez ce dépôt en utilisant la commande suivante :

```bash 
git clone https://github.com/cavmandos/P6.git
```

3.  Installez les dépendances avec composer :

```bash
composer install
```

4.  Créez la base de données et effectuez les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5.  Lancez votre serveur :

```bash
symfony serve
```

6.  Se rendre sur son navigateur favori à l'url indiquée