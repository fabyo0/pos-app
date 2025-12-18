# POS System

A modern, feature-rich Point of Sale (POS) system built with Laravel 12, Filament 4, and Livewire 3.

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4-orange.svg)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-pink.svg)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

---

## 📸 Screenshots

<details>
<summary>Click to view screenshots</summary>

### Dashboard
<img width="1431" height="733" alt="Screenshot 2025-12-18 at 16 58 27" src="https://github.com/user-attachments/assets/8397546c-6b20-4756-b439-eaf39883393b" />

### POS Interface
<img width="1419" height="700" alt="Screenshot 2025-12-18 at 16 59 21" src="https://github.com/user-attachments/assets/88ce467d-7a1a-45e9-8706-506d263c9df4" />

### Sales Management
<img width="1418" height="732" alt="Screenshot 2025-12-18 at 17 00 07" src="https://github.com/user-attachments/assets/808f6c53-8fcc-44dc-b75f-1feac06694d9" />

### Item Management
<img width="1201" height="690" alt="Screenshot 2025-12-18 at 17 01 11" src="https://github.com/user-attachments/assets/a8ba3547-4b7f-4905-8285-b3a82221525d" />

</details>

---

## ✨ Features

### Core POS Functionality
- 🛒 **Product Management** - Complete CRUD with categories
- 🛍️ **Shopping Cart** - Real-time calculations and updates
- 💳 **Multiple Payment Methods** - Cash, Card, Bank Transfer
- 💰 **Discount System** - Percentage & fixed amount discounts
- 👤 **Customer Management** - Track customer purchases
- 📊 **Stock Tracking** - Real-time inventory validation
- 🔍 **Advanced Search** - Quick product search with filters

### Dashboard & Reporting
- 📈 **Sales Statistics** - Comprehensive sales analytics
- 💵 **Revenue Tracking** - Daily, weekly, monthly reports
- 📦 **Inventory Overview** - Stock levels at a glance
- 🔔 **Low Stock Alerts** - Automated notifications

### User Interface
- 🎨 **Modern Design** - Clean and professional UI
- ⚡ **Fast & Intuitive** - Optimized for speed
- 📱 **Mobile Responsive** - Works on all devices
- 🌙 **Filament Admin** - Powerful admin panel

---

## 🚀 Demo

**Live Demo:** [https://pos-app-yi7setba.on-forge.com](https://pos-app-yi7setba.on-forge.com/login)

**Demo Credentials:**
- **Email:** admin@example.com
- **Password:** 123

---

## 📋 Requirements

Before you begin, ensure your system meets the following requirements:

- **PHP** >= 8.3
- **Composer** >= 2.0
- **MySQL** >= 8.0 (or MariaDB >= 10.3)
- **Node.js** >= 20.0 & **npm** >= 10.0
- **Git**

### Recommended PHP Extensions
```
php-mbstring
php-xml
php-bcmath
php-curl
php-gd
php-mysql
php-zip
php-intl
```

---

## ⚙️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/fabyo0/pos-app.git
cd pos-app
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database:
```bash
mysql -u root -p
CREATE DATABASE pos_system;
exit;
```

### 5. Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 6. Create Storage Symlink
```bash
php artisan storage:link
```

### 7. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start the Application
```bash
php artisan serve
```

Visit: http://localhost:8000

---

## 🔐 Default Credentials

After seeding, you can log in with:

- **Email:** admin@admin.com
- **Password:** password

---

## 🧪 Running Tests

This project uses **PestPHP** for testing.

### Run All Tests
```bash
php artisan test
```

Or using Pest directly:
```bash
./vendor/bin/pest
```

### Run Specific Test Suite
```bash
# Feature tests only
php artisan test --testsuite=Feature

# Unit tests only
php artisan test --testsuite=Unit
```

### Test with Coverage
```bash
php artisan test --coverage
```

### Testing Database

Configure a separate testing database in `.env.testing`:
```env
DB_CONNECTION=mysql
DB_DATABASE=pos_system_testing
```

---

## 🛠️ Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| Laravel | 12.x | Backend Framework |
| Filament | 4.x | Admin Panel |
| Livewire | 3.x | Frontend Reactivity |
| MySQL | 8.x | Database |
| Tailwind CSS | 3.x | Styling |
| Alpine.js | 3.x | JavaScript Framework |
| Vite | 5.x | Asset Bundling |

---

## 🗺️ Roadmap

### v1.0.0 - Initial Release (Completed)
- Core POS functionality
- Product management with categories
- Shopping cart and checkout
- Multiple payment methods
- Basic reporting dashboard
- Stock tracking

### v1.1.0 - Core Features (In Progress)
- Database Notifications
- Receipt/Invoice System
- Role & Permission Management
- Order Management
- Social Authentication
- Barcode Scanner Support
- Sales List Improvements

### v1.2.0 - Operations & Cash Management (Planned)
- Cash Register Management
- Refund/Return System
- Hold/Park Orders

### v1.3.0 - Inventory & Analytics (Planned)
- Advanced Inventory Management
- Advanced Reporting & Analytics Dashboard

### v1.4.0 - Customer & Marketing (Planned)
- Customer CRM
- Discount & Promotion System
- Multi-Language Support

### v1.5.0 - Employee & Finance (Planned)
- Expense Management
- Employee/Cashier Management

### v2.0.0 - Enterprise Features (Future)
- Multi-Store Management
- Offline Mode
- API & Webhooks
- Accounting Integration
- Restaurant Mode
- E-commerce Integration
- Mobile App

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 🐛 Bug Reports & Feature Requests

Found a bug or have a feature request? Please [open an issue](https://github.com/fabyo0/pos-app/issues/new).

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Filament](https://filamentphp.com) - Admin Panel
- [Livewire](https://livewire.laravel.com) - Reactive Components
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS

---

## 💖 Support

If you find this project helpful, please give it a ⭐️!

---

## 📊 Project Stats

![GitHub stars](https://img.shields.io/github/stars/fabyo0/pos-app?style=social)
![GitHub forks](https://img.shields.io/github/forks/fabyo0/pos-app?style=social)
![GitHub issues](https://img.shields.io/github/issues/fabyo0/pos-app)
![GitHub pull requests](https://img.shields.io/github/issues-pr/fabyo0/pos-app)

---

**Built with ❤️ by [Fabyo](https://github.com/fabyo0)**
