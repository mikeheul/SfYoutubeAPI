# Symfony YouTube API

Ce projet est une application Symfony permettant d'interagir avec l'API YouTube v3 pour rechercher des chaînes, afficher leurs statistiques et gérer une liste de favoris.

## Fonctionnalités

✅ Rechercher une chaîne YouTube par son `@handle`  
✅ Afficher les statistiques d'une chaîne (nombre d'abonnés, vues, vidéos, etc.)  
✅ Ajouter une chaîne aux favoris  
✅ Supprimer une chaîne des favoris  
✅ Voir la liste des favoris  
✅ Afficher la dernière vidéo d'une chaîne en mode "embed"  

## Installation

1. **Cloner le projet**
   ```sh
   git clone https://github.com/mikeheul/SfYoutubeAPI.git
   cd SfYoutubeAPI
   ```

2. **Installer les dépendances**
   ```sh
   composer install
   npm install
   ```

3. **Configurer l'environnement**  
   Créer un fichier `.env.local` et ajouter ses propres variables :
   ```ini
   APP_ENV=dev
   DATABASE_URL="mysql://root:root@127.0.0.1:3306/youtube_db"
   YOUTUBE_API_KEY="api_key_youtube"
   ```

4. **Créer la base de données et exécuter les migrations**
   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Lancer le serveur Symfony**
   ```sh
   symfony server:start
   ```

## Configuration de l'API YouTube

Le projet utilise l'API YouTube v3.  
Vous devez créer une clé API sur [Google Cloud Console](https://console.cloud.google.com/) et l'ajouter dans le fichier `.env.local`.

## Endpoints principaux

### Recherche d'une chaîne par handle
- **GET** `/youtube/{handle}`
- **Retourne** les statistiques de la chaîne  

### Ajouter une chaîne aux favoris
- **POST** `/youtube/favoris/{channelId}`
- **Données requises** : `{"customUrl": "@handle"}`  

### Supprimer une chaîne des favoris
- **DELETE** `/youtube/favoris/{channelId}`  

### Afficher la liste des favoris
- **GET** `/youtube/favoris`  

### Afficher la dernière vidéo d'une chaîne
- **GET** `/youtube/last-video/{channelId}`  
 
