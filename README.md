# Home Hardware Store Database

This document provides instructions on how to set up the database for the Home Hardware Store application.

## Prerequisites

- MySQL or MariaDB server installed and running.

## Database Setup

1. **Open a terminal or command prompt.**

2. **Log in to your MySQL or MariaDB server as the root user:**

   ```bash
   mysql -u root -p
   ```

   You will be prompted to enter your root password.

3. **Create the database by importing the `schema.sql` file:**

   ```sql
   source /path/to/your/project/schema.sql;
   ```

   Replace `/path/to/your/project/` with the actual path to the `schema.sql` file.

4. **Verify that the database and tables have been created:**

   ```sql
   USE hardware_store;
   SHOW TABLES;
   ```

   You should see a list of all the tables created by the script.

## Database Schema

The database schema consists of the following tables:

- `Users`: Stores user information and their roles.
- `Suppliers`: Stores information about product suppliers.
- `Products`: Stores information about the products.
- `Barcodes`: Stores barcodes for each product.
- `Customers`: Stores customer information.
- `Purchases`: Records purchases made from suppliers.
- `Sales`: Records sales made to customers.
- `Invoices`: Stores invoice information related to sales.
- `Inventory`: Manages the stock of products in the warehouse.
- `Returns`: Records customer returns.
- `StockManagement`: Tracks the movement of stock.
