# MyEcommerce

Site ecommerce

## Environnement de développement

### Pré-requis

* PHP 8.0.2
* Composer
* Symfony CLI

Vous pouvez vérifier les pré-requis avec la commande suivante (de la CLI symfony) :
```bash
symfony check:requirements
```
* Clé API Stripe à récupérer après la création d'un compte sur https://stripe.com/fr

Une fois votre clé Stripe d'api récupérée. (Nécessaire pour la partie paiement du site)
Remplacer la valeur de la variable d'environnement **API_KEY_STRIPE** du fichier .env 

* Création d'une base de données

Renseignez **DATABASE_URL** du fichier .env en fonction de votre SGBDR puis:
*Effacer les fichiers version du dossier migrations si besoin*


### Lancer l'environnement de dévoloppement

```bash
composer install
```
```bash2
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
```
```bash3
symfony serve
```
Rendez-vous sur l'url que cette dernière commande vous renseigne.