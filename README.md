# Home Hardware Store Database

This document provides instructions on how to set up the databases for the Home Hardware Store application.

## Prerequisites

- MySQL or MariaDB server installed and running.

## Database Setup

1. **Open a terminal or command prompt.**

2. **Log in to your MySQL or MariaDB server as the root user:**

   ```bash
   mysql -u root -p
   ```

   You will be prompted to enter your root password.

3. **Create the databases by importing the `schema.sql` file:**

   ```sql
   source /path/to/your/project/schema.sql;
   ```

   Replace `/path/to/your/project/` with the actual path to the `schema.sql` file. This will create four databases: `users_db`, `suppliers_db`, `inventory_db`, and `customers_db`.

4. **Verify that the databases and tables have been created.** You can do this by running `SHOW DATABASES;` and then `USE <database_name>;` and `SHOW TABLES;` for each database.

## Database Schema

The database schema is divided into four separate databases:

### `users_db`

- `Users`: Stores user information and their roles.

### `suppliers_db`

- `Suppliers`: Stores information about product suppliers.
- `Purchases`: Records purchases made from suppliers.

### `inventory_db`

- `Products`: Stores information about the products.
- `Barcodes`: Stores barcodes for each product.
- `Inventory`: Manages the stock of products in the warehouse.
- `StockManagement`: Tracks the movement of stock.

### `customers_db`

- `Customers`: Stores customer information.
- `Sales`: Records sales made to customers.
- `Invoices`: Stores invoice information related to sales.
- `Returns`: Records customer returns.

**Important Note:** The current database design separates tables into different databases. This means that foreign key constraints between tables in different databases (e.g., between `Products` in `inventory_db` and `Suppliers` in `suppliers_db`) are not enforced by the database. The application logic will need to handle these relationships.
