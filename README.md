# Hardware Shop Management System

This is a full-featured web application for managing a hardware shop and its warehouse, built with PHP and MySQL.

## Features

*   **User & Access Management:** Role-based access control (Admin, Manager, Supervisor, Warehouse Associate).
*   **Dashboard:** Sales overview, low stock alerts, and quick actions.
*   **Product & Inventory Management:** Add, edit, delete products with images, categories, and stock tracking.
*   **Sales Module:** POS interface for creating new sales.
*   **Purchase Module:** Manage purchase orders and update stock automatically.
*   **Returns & Refunds:** Handle returns from customers and to suppliers.
*   **Warehouse & Stock Movement:** Track stock movements between locations.
*   **Supplier Management:** Manage supplier information and purchase history.
*   **Customer Management:** (Optional) Manage customer information and purchase history.
*   **Reports & Analytics:** Generate reports for sales, purchases, and inventory valuation.
*   **Dark Mode / Light Mode:** Toggle between dark and light themes.
*   **Data Export:** Export reports to CSV.

## Deployment Instructions

### Prerequisites

*   A web server with PHP support (e.g., Apache, Nginx).
*   A MySQL or MariaDB database server.
*   `php-mysql` extension enabled.

### 1. Database Setup

1.  Create a new database in your MySQL/MariaDB server. For example, you can name it `hardware_shop`.
2.  Import the `config/database.sql` file into your newly created database. This will create all the necessary tables.

    ```bash
    mysql -u your_username -p hardware_shop < config/database.sql
    ```

### 2. Application Configuration

1.  Open the `config/config.php` file.
2.  Update the database credentials to match your environment:

    ```php
    define('DB_HOST', 'your_database_host'); // e.g., 'localhost'
    define('DB_NAME', 'hardware_shop');
    define('DB_USER', 'your_database_user');
    define('DB_PASS', 'your_database_password');
    ```

### 3. Web Server Configuration

1.  Copy all the application files to your web server's document root (e.g., `/var/www/html`).
2.  Ensure your web server has write permissions for the `public/uploads` directory, as this is where product images and invoice attachments will be stored.
3.  Access the application through your web browser. You should be redirected to the login page at `templates/login.php`.

### 4. (Optional) Barcode Generation

If you want to enable barcode generation, you will need to install Composer and the required dependency:

1.  Install Composer by following the instructions at [getcomposer.org](https://getcomposer.org/download/).
2.  Run the following command in the root directory of the application:

    ```bash
    php composer.phar install
    ```
    or if you have composer installed globally
    ```bash
    composer install
    ```

This will install the `picqer/php-barcode-generator` library, and the barcode functionality will be enabled.