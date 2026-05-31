# 🚗 Car Marketplace

A PHP-based car marketplace web application with role-based access control for Admins, Sellers, and Customers.

## Features

### 🔐 Authentication
- User registration and login
- Session-based authentication
- Role-based redirection after login

### 👤 Roles

| Role | Capabilities |
|------|-------------|
| **Admin** | View all users, block/unblock users, delete users |
| **Seller** | Add, edit, delete car listings; manage profile |
| **Customer** | Browse car listings, view car details |

## Tech Stack

- **Backend:** PHP (MVC Architecture)
- **Database:** MySQL (via custom `Database` class)
- **Session Management:** PHP native sessions
- **Frontend:** PHP-rendered views

## Project Structure

```
├── index.php                  # Front controller / router
├── config/
│   └── Database.php           # Database connection
├── models/
│   ├── User.php
│   └── Car.php
├── controllers/
│   ├── AuthController.php     # Login, signup, logout
│   ├── AdminController.php    # User management
│   ├── SellerController.php   # Car listing management
│   ├── CustomerController.php # Browse listings
│   └── PageController.php     # Static pages (About, etc.)
└── views/                     # (your view files here)
```

## Getting Started

### Prerequisites
- PHP 7.4+
- MySQL
- Apache or Nginx (or use PHP's built-in server)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/your-repo-name.git
   cd your-repo-name
   ```

2. **Set up the database**
   - Create a MySQL database
   - Import the SQL schema (add your schema file path here)
   - Update `config/Database.php` with your DB credentials

3. **Run the app**
   ```bash
   php -S localhost:8000
   ```
   Then open [http://localhost:8000](http://localhost:8000) in your browser.

## Routing

All requests go through `index.php` using the `?action=` query parameter.

| Action | Description |
|--------|-------------|
| `login` | Login page |
| `signup` | Registration page |
| `adminUsers` | Admin: manage users |
| `sellerProfile` | Seller: view profile & listings |
| `sellerAddCar` | Seller: add a car |
| `sellerEditCar` | Seller: edit a car |
| `customerCars` | Customer: browse all cars |
| `carDetails` | Customer: view car details |
| `about` | About page |

## Contributing

Pull requests are welcome. For major changes, please open an issue first.


