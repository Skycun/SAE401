# SAE 401

## API Documentation

### Introduction
Cette API RESTful permet l'accès aux données de notre système de gestion de magasins, incluant les marques, catégories, produits, employés, stocks et magasins.

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

#### 🏬 Stores

| Méthode | URL | Description |
|--------|-----|-------------|
| GET | `/stores` | Récupérer tous les magasins |
| GET | `/stores/{id}` | Récupérer un magasin par ID |
| GET | `/stores/search/{query}` | Rechercher des magasins par nom |
| POST | `/stores` | Créer un nouveau magasin |
| PUT | `/stores` | Mettre à jour un magasin |
| DELETE | `/stores` | Supprimer un magasin |

#### 📊 Stocks

| Méthode | URL | Description |
|--------|-----|-------------|
| GET | `/stocks` | Récupérer tous les stocks |
| GET | `/stocks/{id}` | Récupérer un stock par ID |
| GET | `/stocks/product/{product_id}` | Récupérer les stocks par produit |
| GET | `/stocks/store/{store_id}` | Récupérer les stocks par magasin |
| GET | `/stocks/quantity/min/{quantity}` | Récupérer les stocks avec une quantité minimale |
| GET | `/stocks/quantity/max/{quantity}` | Récupérer les stocks avec une quantité maximale |
| GET | `/stocks/quantity/range/{min}/{max}` | Récupérer les stocks dans une fourchette de quantité |
| POST | `/stocks` | Créer un nouveau stock |
| PUT | `/stocks` | Mettre à jour un stock |
| DELETE | `/stocks` | Supprimer un stock |

### Authentification

Toutes les requêtes à l'API nécessitent une clé d'API valide. Ajoutez l'en-tête `Api` à vos requêtes :

```
Api: e8f1997c763
```


### Diagramme des relations entre entités

```mermaid
erDiagram
    Brands ||--o{ Products : "possède"}
    Categories ||--o{ Products : "contient"}
    Products ||--o{ Stocks : "est disponible en"}
    Stores ||--o{ Stocks : "possède"}
    Stores ||--o{ Employees : "emploie"}
    
    Brands {
        int brand_id PK
        string brand_name
    }
    
    Categories {
        int category_id PK
        string category_name
    }
    
    Products {
        int product_id PK
        string product_name
        int brand_id FK
        int category_id FK
        int model_year
        float list_price
    }
    
    Employees {
        int employee_id PK
        string employee_name
        string employee_email
        string employee_password
        string employee_role
        int store_id FK
    }
    
    Stores {
        int store_id PK
        string store_name
        string phone
        string email
        string street
        string city
        string state
        string zip_code
    }
    
    Stocks {
        int store_id PK,FK
        int product_id PK,FK
        int quantity
    }