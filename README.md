# User Dashboard (Laravel 12 + Inertia.js + Vue 3 + TypeScript)

A user management dashboard built with Laravel 12 on the back end and Inertia.js + Vue 3 (TypeScript) on the front end. It supports:

- **1 million+ user records** seeded in batches  
- Fast, multi‑field search (first name, last name, email, city, country)  
- Paginated listing
- Create, view, edit, and delete users (with address) via modals  
---------------------------------------
### User dashboard
![User dashboard](/myApp/Images/dashboard.png)

### Searching, pagination
![User dashboard](/myApp/Images/Search1.png)

![User dashboard](/myApp/Images/Search2.png)

### Create User
![User dashboard](/myApp/Images/addNewUser.png)

### View User
![User dashboard](/myApp/Images/viewUser.png)

### Edit User
![User dashboard](/myApp/Images/editUser1.png)
![User dashboard](/myApp/Images/editUser2.png)
![User dashboard](/myApp/Images/editUser3.png)

### Delete User
![User dashboard](/myApp/Images/deleteUser.png)
---------------------------------------


## Project Structure
### Backend
```
app/
├── Http/
│   ├── Controllers/Api     # API controllers
│   ├── Middleware/         # HTTP middleware
│   └── Requests/           # Request validation
├── Contracts/              
│   ├── User/               # User-related contracts
│   └── Repositories/       # Repository contracts
├── Services/User/          # User-related services
├── Repositories/           # User-related repositories
├── Models/                 # Eloquent models
├── Config/                 # Resuable config data
└── Providers/              # Service-container bindings

database/
├── factories/              # Model factories for seeding/testing
├── migrations/             # Database migrations
└── seeders/                # Database seeders

routes/
├── api.php                 # API routes
└── web.php                 # Web routes

tests/
├── Feature/
└── Unit/

```
### Frontend
```
resources/js/
├─ pages/users/Index.vue      # User listing page
├─ components/
│   ├─ UserCreateModal.vue    # User create modal
│   ├─ UserDetailModal.vue    # User detail modal for viewing and editing
│   ├─ Loader.vue             # Loader component
│   └─ DeleteConfirmModal.vue # Delete confirmation modal
├─ composables/useApi.ts      # API request helper
└─ services/api.ts            # API request wrapper

```

## **API Endpoints**

## 1) **List & search users (paginated)**
```bash
GET /api/v1/users
```
```
Query parameters

| Name      | Type    | Required | Description                                              |
| --------- | ------- | -------- | -------------------------------------------------------- |
| search    | string  | no       | Filter by first_name, last_name, email, city or country. |
| per_page  | integer | no       | Results per page (default 15, max 100).                  |
| page      | integer | no       | Page number (default 1).                                 |
```
### Success Response
```
{
  "data": [
    {
      "id": 123,
      "first_name": "Alice",
      "last_name": "Smith",
      "email": "alice@example.com",
      "created_at": "2025-05-07T12:34:56Z",
      "updated_at": "2025-05-07T12:34:56Z",
      "address": {
        "id": 456,
        "user_id": 123,
        "country": "Canada",
        "city": "Toronto",
        "post_code": "M4B1B3",
        "street": "123 Example St",
        "created_at": "2025-05-07T12:34:56Z",
        "updated_at": "2025-05-07T12:34:56Z"
      }
    }
    // …
  ],
  "current_page": 1,
  "last_page": 10,
  "per_page": 15,
  "total": 150
}
```
## 2) **Fetch a single user**
```bash
GET /api/v1/users/{id}
```
```
Parameters

| Name | Type    | Required | Description     |
| ---- | ------- | -------- | --------------- |
| id   | integer | yes      | ID of the user. |
```
### Success Response
```
{
  "id": 123,
  "first_name": "Alice",
  "last_name": "Smith",
  "email": "alice@example.com",
  "email_verified_at": "2025-05-07T12:34:56Z",
  "created_at": "2025-05-07T12:34:56Z",
  "updated_at": "2025-05-07T12:34:56Z",
  "address": {
    "id": 456,
    "user_id": 123,
    "country": "Canada",
    "city": "Toronto",
    "post_code": "M4B1B3",
    "street": "123 Example St",
    "created_at": "2025-05-07T12:34:56Z",
    "updated_at": "2025-05-07T12:34:56Z"
  }
}
```
### Error response (404 Not Found)
```
{
  "message": "Not found"
}

```

## 3) **Create a new user**
```bash
POST /api/v1/users
Content-Type: application/json
```

```
 POST /api/v1/users

{
  "first_name": "Bob",
  "last_name": "Jones",
  "email": "bob.jones@example.com",
  "password": "secret123",
  "country": "Canada",
  "city": "Vancouver",
  "post_code": "V5K0A1",
  "street": "456 Demo Rd"
}
```
### Successful response (201 Created)
``` 
{
  "id": 1000001,
  "first_name": "Bob",
  "last_name": "Jones",
  "email": "bob.jones@example.com",
  "email_verified_at": "2025-05-09T13:00:00Z",
  "created_at": "2025-05-09T13:00:00Z",
  "updated_at": "2025-05-09T13:00:00Z",
  "address": {
    "id": 1000001,
    "user_id": 1000001,
    "country": "Canada",
    "city": "Vancouver",
    "post_code": "V5K0A1",
    "street": "456 Demo Rd",
    "created_at": "2025-05-09T13:00:00Z",
    "updated_at": "2025-05-09T13:00:00Z"
  }
}
```
### Validation error (422 Unprocessable Entity)
```
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}

```
## 4) Update an existing user
```bash
PUT /api/v1/users/{id}
Content-Type: application/json
```

```
PUT /api/v1/users/123

{
  "first_name": "Robert",
  "last_name":  "Jones",
  "email":      "robert.jones@example.com",
  "password":   "newsecret",           // optional—omit to keep existing
  "country":    "Canada",
  "city":       "Vancouver",
  "post_code":  "V5K0A1",
  "street":     "789 Updated Ave"
}
```
### Successful response (200 OK)
```
{
  "id": 123,
  "first_name": "Robert",
  "last_name": "Jones",
  "email": "robert.jones@example.com",
  "email_verified_at": "2025-05-09T13:00:00Z",
  "created_at": "2025-05-08T10:00:00Z",
  "updated_at": "2025-05-09T13:15:00Z",
  "address": {
    "id": 456,
    "user_id": 123,
    "country": "Canada",
    "city": "Vancouver",
    "post_code": "V5K0A1",
    "street": "789 Updated Ave",
    "created_at": "2025-05-08T10:00:00Z",
    "updated_at": "2025-05-09T13:15:00Z"
  }
}
```
### Validation error (422 Unprocessable Entity)
```
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken by another user."],
    "first_name": ["The first name field is required."]
  }
}
```

## 5) Delete a user
```bash
DELETE /api/v1/users/{id}
```
### Successful response (204 No Content)

- The user (and its address via cascade) has been removed.
- Response body is empty.

### Error response (404 Not Found)
```
{
  "message": "Not found"
}
```



## Prerequisites

- PHP 8.2+, Composer  
- MySQL 8+ 
- Node.js 16+, npm  


## ⚙️ Installation

1. **Clone repository**  
   ```bash
   git clone <url>
   cd <pjt directory>
   ```
2. **Install PHP dependencies**
   ```bash
   composer install
   ```
3. **Configure database in .env (MySQL credentials).**

4. **Migrate & seed**
   ```bash
    php artisan migrate
    php artisan db:seed          # runs batched UserWithAddressSeeder
   ```
5. **Install Node dependencies**
   ```bash
    npm install
    ```
6. **serve**
   ```bash
    composer run dev
    ```


## Running Tests

- Create a `.env.testing` file copying `.env`
- Make sure your `.env.testing` is configured (SQLite in-memory) then:

### Run only unit tests
```bash
./vendor/bin/phpunit --filter UserServiceTest
```

### Run only feature tests
```bash
./vendor/bin/phpunit --filter UserControllerTest
``` 

## Notes
