-- Create the database
CREATE DATABASE IF NOT EXISTS hardware_store;

-- Use the database
USE hardware_store;

-- Create the Users table
CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('Admin', 'Manager', 'Supervisor', 'Warehouse Associate') NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the Suppliers table
CREATE TABLE Suppliers (
    SupplierID INT PRIMARY KEY AUTO_INCREMENT,
    SupplierName VARCHAR(100) NOT NULL,
    ContactName VARCHAR(100),
    Phone VARCHAR(20),
    Email VARCHAR(100),
    Address TEXT
);

-- Create the Products table
CREATE TABLE Products (
    ProductID INT PRIMARY KEY AUTO_INCREMENT,
    ProductName VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10, 2) NOT NULL,
    SupplierID INT,
    FOREIGN KEY (SupplierID) REFERENCES Suppliers(SupplierID)
);

-- Create the Barcodes table
CREATE TABLE Barcodes (
    BarcodeID INT PRIMARY KEY AUTO_INCREMENT,
    ProductID INT,
    Barcode VARCHAR(255) NOT NULL UNIQUE,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);

-- Create the Customers table
CREATE TABLE Customers (
    CustomerID INT PRIMARY KEY AUTO_INCREMENT,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(20)
);

-- Create the Purchases table
CREATE TABLE Purchases (
    PurchaseID INT PRIMARY KEY AUTO_INCREMENT,
    SupplierID INT,
    PurchaseDate DATE NOT NULL,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (SupplierID) REFERENCES Suppliers(SupplierID)
);

-- Create the Sales table
CREATE TABLE Sales (
    SaleID INT PRIMARY KEY AUTO_INCREMENT,
    CustomerID INT,
    SaleDate DATE NOT NULL,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID)
);

-- Create the Invoices table
CREATE TABLE Invoices (
    InvoiceID INT PRIMARY KEY AUTO_INCREMENT,
    SaleID INT,
    InvoiceDate DATE NOT NULL,
    DueDate DATE,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    Status ENUM('Paid', 'Unpaid', 'Overdue') NOT NULL,
    FOREIGN KEY (SaleID) REFERENCES Sales(SaleID)
);

-- Create the Inventory table
CREATE TABLE Inventory (
    InventoryID INT PRIMARY KEY AUTO_INCREMENT,
    ProductID INT,
    Quantity INT NOT NULL,
    Location VARCHAR(100),
    LastUpdated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);

-- Create the Returns table
CREATE TABLE Returns (
    ReturnID INT PRIMARY KEY AUTO_INCREMENT,
    SaleID INT,
    ReturnDate DATE NOT NULL,
    Reason TEXT,
    FOREIGN KEY (SaleID) REFERENCES Sales(SaleID)
);

-- Create the StockManagement table
CREATE TABLE StockManagement (
    StockMovementID INT PRIMARY KEY AUTO_INCREMENT,
    ProductID INT,
    UserID INT,
    MovementType ENUM('IN', 'OUT') NOT NULL,
    Quantity INT NOT NULL,
    MovementDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
