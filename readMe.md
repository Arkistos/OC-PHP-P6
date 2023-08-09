# SnowTricks - Blog de Figures de Snowboard

SnowTricks est un projet Symfony qui permet aux passionnés de snowboard de partager et de découvrir de nouvelles figures, de les commenter et de les modifier. Que vous soyez un rider expérimenté ou un débutant curieux, SnowTricks est l'endroit idéal pour explorer l'univers du snowboard.

## Fonctionnalités

- **Authentification et Autorisation :** Les utilisateurs peuvent s'inscrire, se connecter et se déconnecter. Les utilisateurs connectés ont la possibilité de créer de nouvelles figures, de modifier les figures existantes et de commenter les figures.

- **Exploration des Figures :** Parcourez une collection de figures de snowboard. Chaque figure est accompagnée d'une description, de photos et de vidéos.

- **Création et Modification de Figures :** Les utilisateurs connectés peuvent ajouter de nouvelles figures à la base de données ou modifier les détails des figures existantes. Vous pouvez également télécharger des médias pour illustrer vos figures.

- **Commentaires :** Exprimez vos pensées et partagez des astuces en commentant les figures. Les discussions facilitent les interactions entre les membres de la communauté.

## Installation

1. Clonez le dépôt GitHub :

```bash
git clone https://github.com/votre-utilisateur/snowtricks.git
```

2. Installer les dépendances via Composer :

```bash
composer install
```

3. Configurer la base de données dans le fichier .env :

```dotenv
DATABASE_URL="mysql://username:password@localhost:3306/db_name?serverVersion=8.0.32&charset=utf8mb4"
```

4. Créez la base de données et les tables:

```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

5. Lancez le serveur de développement :
```bash
symfony console server:start
```
