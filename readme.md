# SAE 401

## API Documentation

### Introduction
This RESTful API allows access to our store management system data, including brands, categories, products, employees, stocks, and stores.

### Available Endpoints

#### 📦 Brands

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/brands` | Get all brands |
| GET | `/brands/{id}` | Get a specific brand by ID |
| GET | `/brands/search/{query}` | Search brands by name |
| POST | `/brands` | Create a new brand |
| PUT | `/brands` | Update a brand |
| DELETE | `/brands` | Delete a brand |

```json
{
  "id": 1,              // Required for PUT requests
  "brand_name": "Trek"  // Required string
}
```

#### 📚 Categories

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/categories` | Get all categories |
| GET | `/categories/{id}` | Get a specific category by ID |
| GET | `/categories/search/{query}` | Search categories by name |
| POST | `/categories` | Create a new category |
| PUT | `/categories` | Update a category |
| DELETE | `/categories` | Delete a category |

```json
{
  "id": 1,                   // Required for PUT requests
  "category_name": "Mountain Bikes"  // Required string
}
```

#### 🛍️ Products

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/products` | Get all products |
| GET | `/products/{id}` | Get a specific product by ID |
| GET | `/products/search/{query}` | Search products by name |
| GET | `/products/category/{category_id}` | Get products by category |
| GET | `/products/brand/{brand_id}` | Get products by brand |
| POST | `/products` | Create a new product |
| PUT | `/products` | Update a product |
| DELETE | `/products` | Delete a product |

```json
{
  "id": 1,                      // Required for PUT requests
  "product_name": "Trail Bike", // Optional string
  "brand_id": 1,                // Optional integer - must exist in brands table
  "category_id": 2,             // Optional integer - must exist in categories table
  "model_year": 2023,           // Optional integer
  "list_price": 1299.99         // Optional float
}
```

#### 👥 Employees

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/employees` | Get all employees |
| GET | `/employees/{id}` | Get a specific employee by ID |
| GET | `/employees/search/{query}` | Search employees by name |
| GET | `/employees/store/{store_id}` | Get employees by store |
| POST | `/employees/login` | Authenticate an employee, and return the employees object |
| POST | `/employees` | Create a new employee |
| PUT | `/employees` | Update an employee |
| DELETE | `/employees` | Delete an employee |

```json
{
  "id": 1,                                // Required for PUT requests
  "employees_name": "John Doe",           // Optional string
  "employees_email": "john@example.com",  // Optional string (must be unique)
  "employees_password": "secure123",      // Optional string
  "employees_role": "manager",            // Optional string
  "store_id": 1                           // Optional integer - must exist in stores table
}
```

#### 🏬 Stores

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/stores` | Get all stores |
| GET | `/stores/{id}` | Get a specific store by ID |
| GET | `/stores/search/{query}` | Search stores by name |
| POST | `/stores` | Create a new store |
| PUT | `/stores` | Update a store |
| DELETE | `/stores` | Delete a store |

```json
{
  "id": 1,                              // Required for PUT requests
  "store_name": "Downtown Bike Shop",   // Optional string
  "phone": "(123) 456-7890",            // Optional string
  "email": "contact@bikestore.com",     // Optional string
  "street": "123 Main St",              // Optional string
  "city": "New York",                   // Optional string
  "state": "NY",                        // Optional string
  "zip_code": "10001"                   // Optional string
}
```

#### 📊 Stocks

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/stocks` | Get all stocks |
| GET | `/stocks/{id}` | Get a specific stock by ID |
| GET | `/stocks/product/{product_id}` | Get stocks by product |
| GET | `/stocks/store/{store_id}` | Get stocks by store |
| GET | `/stocks/page/{page_number}` | Get paginated stocks |
| POST | `/stocks` | Create a new stock |
| PUT | `/stocks` | Update a stock |
| DELETE | `/stocks` | Delete a stock |

```json
{
  "id": 1,          // Required for PUT requests
  "store_id": 1,    // Optional integer - must exist in stores table
  "product_id": 1,  // Optional integer - must exist in products table
  "quantity": 10    // Optional integer
}
```

### Authentication

Some API requests will require a valid API key (PUT, DELETE, POST and some GET).
Add the Api header to your requests:

```
Api: e8f1997c763
```