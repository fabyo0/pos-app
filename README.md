# 🚀 Setup Guide

## ✅ Requirements

Make sure the following are installed on your system:

- **PHP** >= 8.0  
- **Composer**  
- **MySQL** (or any other supported database)  
- **Node.js & npm** *(optional, for frontend assets)*

---

## ⚙️ Setup Instructions

### 1. Install Dependencies

Install all PHP dependencies via Composer:

```bash
composer install
```

If the `.env` file is missing, copy it from the example:

```bash
cp .env.example .env
```

---

### 2. Configure the Database

Laravel uses **MySQL** by default. To use a different database, update `config/database.php` and your `.env` file.

#### Steps:
1. Install and configure your preferred database.
2. Create a new database for the project.
3. Update your `.env` file with the correct credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

---

### 3. Generate Application Key

```bash
php artisan key:generate
```

---

### 4. Run Migrations

Run database migrations to create all necessary tables:

```bash
php artisan migrate
```

---

### 5. Seed the Database (Optional)

To populate your database with sample data:

```bash
php artisan db:seed
```

---

### 6. File Uploads - Create Storage Symlink

To make uploaded files publicly accessible:

```bash
php artisan storage:link
```

---

### 7. Run the Application

#### Option 1: Using Laravel's Built-in Server

```bash
php artisan serve
```

#### Option 2: Laravel Valet (Recommended for macOS)

Place your project in the Valet directory and access it via `http://project-name.test`.

---

## 🖼️ Frontend (Optional)

If your project uses Vite, Laravel Mix, or any frontend build system:

```bash
npm install && npm run dev
```

> For production:
```bash
npm run build
```

---

## 📝 Additional Notes

- Don’t forget to set the correct `APP_URL` in your `.env`:
   ```env
   APP_URL=http://localhost:8000
   ```

- Clear caches if necessary:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  ```

---

Happy coding! 🧑‍💻


---

## 🧪 Running Tests

This project may include automated tests using Pestphp.

### Run All Tests

```bash
php artisan test
```

Or directly via Pestphp:

```bash
./vendor/bin/pest
```

### Recommended: Use a dedicated testing database

To avoid affecting development or production data, configure a separate database for testing by adding the following to your `.env`:

```env
DB_DATABASE=your_testing_database
```

Then, in `phpunit.xml`, set the appropriate environment:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="your_testing_database"/>
```

You can also refresh the test database before each test run with Laravel's RefreshDatabase trait.

---

