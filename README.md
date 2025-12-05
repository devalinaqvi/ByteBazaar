# 🖥️ Online Computer Store (Custom PHP MVC Framework)

A lightweight, custom-built MVC framework for an **Online Computer Store**.

## 📚 Student Information
- **Name:** Tabish Hassan
- **Student ID:** 239649410
- **Email:** thassan@algomau.ca
- **Live Demo:** [https://syntaxcamp.com/](https://syntaxcamp.com/) (if applicable)

## ✨ Features

* User & Admin dashboards
* Product management (CRUD)
* Cart system (guest & logged-in)
* AJAX add-to-cart
* Checkout + order creation
* Repositories & Services architecture
* Routing via **AltoRouter**
* PDO MySQL with prepared statements
* Unit testing with **PHPUnit**
* Order management with order items tracking

---

# 📁 Project Structure

```
project-root/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   ├── Factories/
│   ├── Core/              # Router, Container, Database, BaseController
│   └── Views/             # admin/ & user/
├── public/                # Document root for Apache/Nginx
│   ├── index.php
│   ├── assets/
│   └── uploads/
├── config/
│   └── config.php
├── migrations/
│   ├── schema.sql
│   └── seed.php
├── vendor/
├── .htaccess
├── composer.json
└── README.md
```

---

# 🛠️ Requirements

| Software        | Version       |
| --------------- |---------------|
| PHP             | **8.2+**      |
| MySQL           | **5.7 / 8.0** |
| Composer        | **latest**    |
| Apache or Nginx | optional      |

### PHP Extensions Needed

Ensure these are enabled in `php.ini`:

```ini
extension=pdo
extension=pdo_mysql
extension=openssl
extension=mbstring
extension=curl
extension=json
```

---

# 📦 Installation

### 1️⃣ Install Dependencies

```bash
composer install
```

This installs:
* **altorouter/altorouter** — Routing
* **phpunit/phpunit** — Testing
* PSR-4 Autoloading

### 2️⃣ Configure Environment

Edit `config/config.php`:

```php
return [
    'db' => [
        'host' => 'localhost',
        'database' => 'computer_store',
        'user' => 'root',
        'password' => '',
    ],
    'app' => [
        'url' => 'http://localhost:8000',
        'env' => 'local'
    ]
];
```

### 3️⃣ Create Database

Login to MySQL:

```bash
mysql -u root -p
```

Create database:

```sql
CREATE DATABASE computer_store;
USE computer_store;
```

### 4️⃣ Run Migrations

Run the schema file:

```bash
mysql -u root -p computer_store < migrations/schema.sql
```

Or run the seed script:

```bash
php migrations/seed.php
```

**Default Admin Credentials:**
* Email: `thassan@algomau.ca`
* Password: `password123`

---

# 🚀 Running the Application

## ✅ Option A — PHP Built-in Server (Recommended for Development)

Simply run:

```bash
php -S localhost:8000 -t public/
```

Access the application at:

```
http://localhost:8000
```

**This is the quickest way to get started!**

---

## 🔷 Option B — XAMPP (Windows)

1. Copy project to:
```
C:\xampp\htdocs\computer-store\
```

2. Start Apache from XAMPP Control Panel

3. Visit:
```
http://localhost/computer-store/
```

4. If you get "Class not found" errors, run:
```bash
composer dump-autoload
```

---

## 🐧 Option C — Apache (Linux / macOS)

1. Enable rewrite module:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

2. Create virtual host `/etc/apache2/sites-available/computer-store.conf`:
```apache
<VirtualHost *:80>
    ServerName computer-store.local
    DocumentRoot /var/www/computer-store/public

    <Directory /var/www/computer-store/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Enable site:
```bash
sudo a2ensite computer-store
sudo systemctl reload apache2
```

4. Add to `/etc/hosts`:
```
127.0.0.1 computer-store.local
```

5. Access:
```
http://computer-store.local
```

---

## 🐳 Option D — Nginx (Linux / macOS)

Create server block `/etc/nginx/sites-available/computer-store`:
```nginx
server {
    listen 80;
    server_name computer-store.local;

    root /var/www/computer-store/public;
    index index.php;

    location / {
        try_files $uri /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/computer-store /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

Add to `/etc/hosts`:
```
127.0.0.1 computer-store.local
```

Access:
```
http://computer-store.local
```

---

# 📝 Running Tests

```bash
composer test
```

Or manually:
```bash
vendor/bin/phpunit
```

---

# 🗄️ Database Schema

The migrations include:

* **users** — Authentication & authorization
* **categories** — Product categories
* **products** — Product catalog (with soft delete)
* **cart_items** — Shopping cart (session-based & user-based)
* **orders** — Order records with shipping details
* **order_items** — Individual items in each order
* Proper indexes & foreign key constraints
* Guest session_id support for anonymous cart

---

# 🧑‍💻 Development Workflow

### Admin Panel:
* Product management (Create, Read, Update, Delete)
* Category management
* Order management & tracking
* View all orders with item details

### User Features:
* Browse products by category
* Search functionality
* Pagination
* Add to cart (AJAX)
* Checkout with shipping details
* Order history
* Guest checkout support

---

# 🛡️ Security Features

* Password hashing using `password_hash()`
* Session-based authentication
* PDO prepared statements (SQL injection prevention)
* CSRF token support
* Server-side input validation
* Soft deletes for data integrity

---

# 📖 Common Commands

```bash
# Install dependencies
composer install

# Run development server
php -S localhost:8000 -t public/

# Reload autoloader (after adding new classes)
composer dump-autoload

# Run tests
composer test

# Seed database
php migrations/seed.php
```

---

# 🆘 Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
```
### Upload File Size Too Large
* Increase `upload_max_filesize` in `php.ini`

### Nginx 413 Request Entity Too Large
* Increase `client_max_body_size` in `nginx.conf`

### Apache 500 Internal Server Error
* Check Apache error logs: `sudo tail -f /var/log/apache2/error.log`

### Database connection fails
* Check credentials in `config/config.php`
* Ensure MySQL is running
* Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### .htaccess not working
* Enable mod_rewrite: `sudo a2enmod rewrite`
* Restart Apache: `sudo systemctl restart apache2`

### Port 8000 already in use
Use a different port:
```bash
php -S localhost:8001 -t public/
```

### Admin credentials not working
* Check credentials in `migrations/seed.php`

---

# 📞 Support

If you encounter issues:

1. Check error logs in browser console
2. Verify all PHP extensions are enabled
3. Ensure database migrations ran successfully
4. Review credentials in `config/config.php`

---

# 📄 License

This project is for educational purposes.

---

**Happy coding! 🚀**