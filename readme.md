# SAE 401

## API Documentation

### Introduction
Cette API RESTful permet l'accès aux données de notre système de gestion de magasins, incluant les marques, catégories, produits et employés.

### Points d'accès disponibles

#### 📦 Brands

| Méthode | URL | Description |
|--------|-----|-------------|
| GET | `/brands` | Récupérer toutes les marques |
| GET | `/brands/{id}` | Récupérer une marque par ID |
| GET | `/brands/search/{query}` | Rechercher des marques par nom |
| POST | `/brands` | Créer une nouvelle marque |
| PUT | `/brands` | Mettre à jour une marque |
| DELETE | `/brands` | Supprimer une marque |

#### 📚 Categories

| Méthode | URL | Description |
|--------|-----|-------------|
| GET | `/categories` | Récupérer toutes les catégories |
| GET | `/categories/{id}` | Récupérer une catégorie par ID |
| GET | `/categories/search/{query}` | Rechercher des catégories par nom |
| POST | `/categories` | Créer une nouvelle catégorie |
| PUT | `/categories` | Mettre à jour une catégorie |
| DELETE | `/categories` | Supprimer une catégorie |

#### 🛍️ Products

| Méthode | URL | Description |
|--------|-----|-------------|
| GET | `/products` | Récupérer tous les produits |
| GET | `/products/{id}` | Récupérer un produit par ID |
| GET | `/products/search/{query}` | Rechercher des produits par nom |
| GET | `/products/category/{category_id}` | Récupérer les produits par catégorie |
| GET | `/products/brand/{brand_id}` | Récupérer les produits par marque |
| POST | `/products` | Créer un nouveau produit |
| PUT | `/products` | Mettre à jour un produit |
| DELETE | `/products` | Supprimer un produit |

#### 👥 Employees

| Méthode | URL | Description |
|--------|-----|-------------|
| GET | `/employees` | Récupérer tous les employés |
| GET | `/employees/{id}` | Récupérer un employé par ID |
| GET | `/employees/search/{query}` | Rechercher des employés par nom |
| GET | `/employees/store/{store_id}` | Récupérer les employés par magasin |
| POST | `/employees/login` | Authentifier un employé |
| POST | `/employees` | Créer un nouvel employé |
| PUT | `/employees` | Mettre à jour un employé |
| DELETE | `/employees` | Supprimer un employé |

### Authentification

Toutes les requêtes à l'API nécessitent une clé d'API valide. Ajoutez l'en-tête `Api` à vos requêtes :

```
Api: e8f1997c763
```