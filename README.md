# Hardware Store Management System

A comprehensive inventory and sales management system designed specifically for hardware stores, built with PHP and MySQL.

![Hardware Store Dashboard](docs/images/dashboard-preview.png)

## 📋 Table of Contents

- [Features Overview](#features-overview)
- [System Architecture](#system-architecture)
- [Installation](#installation)
- [User Roles & Permissions](#user-roles--permissions)
- [Module Documentation](#module-documentation)
- [Workflow Processes](#workflow-processes)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

## 🚀 Features Overview

### **Core Business Modules**

#### 🏪 **Sales Management**

- **Point of Sale (POS)** - Complete transaction processing
- **Customer Management** - Customer database with purchase history
- **Payment Processing** - Multiple payment methods support
- **Invoice Generation** - Professional invoice creation and printing
- **Sales Analytics** - Daily, weekly, monthly sales reports
- **Return Processing** - Handle returns and refunds

#### 📦 **Inventory Management**

- **Real-time Stock Tracking** - Live inventory updates
- **Location-based Storage** - Multi-location warehouse support
- **Bulk Location Management** - Receiving dock to shelf workflow
- **FIFO Stock Rotation** - First-in-first-out inventory management
- **Low Stock Alerts** - Automated reorder notifications
- **Cycle Counting** - Physical inventory reconciliation
- **Barcode Integration** - Barcode scanning support

#### 🛒 **Purchase Management**

- **Purchase Order Creation** - Digital PO generation
- **Supplier Management** - Vendor database and relationships
- **Receiving Workflow** - Systematic goods receipt process
- **Bulk Location Assignment** - Automatic receiving dock allocation
- **Three-way Matching** - PO, Receipt, Invoice verification
- **Vendor Performance Tracking** - Supplier analytics

#### 📊 **Reporting & Analytics**

- **Financial Reports** - P&L, cash flow, expense tracking
- **Inventory Reports** - Stock levels, valuation, movement history
- **Sales Analytics** - Performance metrics and trends
- **Purchase Analytics** - Vendor performance and cost analysis
- **Custom Dashboard** - Role-based KPI displays

### **Advanced Features**

#### 🏭 **Warehouse Operations**

- **Bulk Receiving Locations** - B-001, B-002 staging areas
- **Location Transfer System** - Bulk to shelf movement tracking
- **Stock Movement Auditing** - Complete movement history
- **Batch/Lot Tracking** - Product batch management
- **Expiry Date Management** - FEFO (First Expired, First Out)

#### 👥 **User Management**

- **Role-based Access Control** - Granular permission system
- **User Profile Management** - Profile pictures and settings
- **Activity Logging** - Complete user action audit trail
- **Password Security** - Secure authentication system

#### 🎨 **User Interface**

- **Responsive Design** - Mobile and desktop optimized
- **Dark/Light Themes** - Multiple theme options
- **Unified Theme System** - Consistent UI across modules
- **Professional Cards** - Modern card-based layouts
- **Interactive Dashboards** - Real-time data visualization

#### 🔧 **System Administration**

- **Company Profile Management** - Business information setup
- **System Configuration** - Application settings
- **Database Migrations** - Version-controlled schema updates
- **Backup & Recovery** - Data protection systems

## 🏗️ System Architecture

### **Technology Stack**

- **Backend**: PHP 7.4+ with MVC Architecture
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Frameworks**: Bootstrap 5, Font Awesome, Chart.js
- **Server**: Apache/Nginx with mod_rewrite

### **Project Structure**

```
hardware-store/
├── app/
│   ├── controllers/         # Business logic controllers
│   ├── models/             # Data access layer
│   ├── views/              # Presentation layer
│   ├── helpers/            # Utility functions
│   └── config/             # Application configuration
├── database/
│   ├── migrations/         # Database schema updates
│   └── seeds/              # Test data
├── public/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── uploads/           # File uploads
└── docs/                   # Documentation
```

### **Database Schema**

#### **Core Tables**

- `users` - User accounts and authentication
- `products` - Product catalog and information
- `stock` - Inventory quantities and locations
- `warehouse_locations` - Storage location definitions
- `sales` - Sales transaction records
- `purchase_orders` - Purchase order management
- `suppliers` - Vendor information

#### **Tracking Tables**

- `stock_movements` - Inventory movement audit trail
- `activity_logs` - User action logging
- `cycle_counts` - Physical inventory records

## 🔐 User Roles & Permissions

### **Admin (Super User)**

- Full system access
- User management
- System configuration
- Financial reporting
- All module access

### **Manager**

- Sales management
- Inventory oversight
- Purchase approvals
- Staff supervision
- Reporting access

### **Supervisor**

- Daily operations
- Stock management
- Sales processing
- Team coordination

### **Sales Clerk**

- POS operations
- Customer service
- Basic inventory checks
- Return processing

### **Stock Clerk**

- Inventory management
- Receiving operations
- Location transfers
- Cycle counting

## 📋 Module Documentation

### **Dashboard Module**

**File**: `app/controllers/DashboardController.php`

**Features**:

- Real-time KPIs (sales, inventory, purchases)
- Interactive charts and graphs
- Recent activity summaries
- Quick action buttons
- Role-based content display

**Key Methods**:

- `index()` - Main dashboard display
- `getKPIData()` - Dashboard metrics
- `getChartData()` - Chart information

### **Sales Module**

**Files**: `app/controllers/SalesController.php`, `app/models/Sale.php`

**Features**:

- Point of sale interface
- Customer selection and management
- Product scanning and selection
- Multiple payment methods
- Receipt printing
- Return processing

**Key Methods**:

- `pos()` - Point of sale interface
- `addSale()` - Process new sale
- `addSaleItem()` - Add items with stock deduction
- `deductStock()` - FIFO inventory deduction

### **Inventory Module**

**Files**: `app/controllers/InventoryController.php`, `app/models/Inventory.php`

**Features**:

- Stock level monitoring
- Location-based tracking
- Bulk transfer operations
- Low stock alerts
- Movement history

**Key Methods**:

- `index()` - Inventory overview
- `bulk_transfer()` - Bulk location transfers
- `getBulkLocationStock()` - Items in receiving areas
- `transferFromBulkLocation()` - Location transfers

### **Purchase Module**

**Files**: `app/controllers/PurchasesController.php`, `app/models/Purchase.php`

**Features**:

- Purchase order creation
- Supplier management
- Receiving workflow
- Bulk location assignment
- Three-way matching

**Key Methods**:

- `receive_items()` - Item receiving interface
- `process_receive()` - Process received items
- `updateProductStockWithLocation()` - Stock updates with location tracking

## 🔄 Workflow Processes

### **Receiving Workflow**

```
1. Purchase Order Created → PO sent to supplier
2. Goods Arrive → Items received at dock
3. Bulk Location Assignment → Items assigned to B-001, B-002, etc.
4. Quality Check → Items inspected and verified
5. Stock Update → Inventory quantities updated
6. Location Transfer → Items moved to sales floor
7. Audit Trail → All movements logged
```

### **Sales Workflow**

```
1. Customer Selection → Identify customer or walk-in
2. Product Scanning → Add items to cart
3. Stock Validation → Check availability in accessible locations
4. Payment Processing → Process payment method
5. Stock Deduction → FIFO deduction from inventory
6. Receipt Generation → Print customer receipt
7. Movement Logging → Record stock movements
```

### **Inventory Transfer Workflow**

```
1. Bulk Location Review → Check items in receiving areas
2. Transfer Planning → Select items and destinations
3. Location Transfer → Move items to sales floor
4. Stock Update → Update location records
5. Movement Logging → Audit trail creation
```

## 🔧 Installation

### **Prerequisites**

- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (for dependencies)

### **Installation Steps**

1. **Clone Repository**

   ```bash
   git clone https://github.com/thenewone567/hardware-store-app.git
   cd hardware-store-app
   ```

2. **Install Dependencies**

   ```bash
   composer install
   ```

3. **Database Setup**

   ```bash
   mysql -u root -p < database/migrations/database.sql
   mysql -u root -p < database/migrations/create_bulk_locations_updated.sql
   ```

4. **Configuration**

   ```php
   // config/database.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'master_hardware');
   ```

5. **Web Server Setup**

   - Point document root to `public/` directory
   - Enable mod_rewrite for clean URLs
   - Set appropriate file permissions

6. **Default Login**
   - Username: `admin`
   - Password: `admin123`

### **System Requirements**

- **PHP Extensions**: PDO, PDO_MySQL, GD, JSON
- **Memory**: Minimum 256MB RAM
- **Storage**: 500MB disk space
- **Browser**: Modern browsers (Chrome, Firefox, Safari, Edge)

## 🎯 Key Features Detail

### **Bulk Location System**

The application implements a realistic warehouse receiving workflow:

- **Bulk Locations**: B-001 through B-010 for receiving dock operations
- **Automatic Assignment**: All received items go to bulk locations first
- **Transfer Interface**: Systematic movement to sales floor locations
- **FIFO Logic**: Sales prioritize regular locations over bulk areas

### **Stock Movement Tracking**

Complete audit trail for all inventory movements:

- **Receiving Movements**: Items entering bulk locations
- **Transfer Movements**: Bulk to regular location transfers
- **Sales Movements**: Stock deduction during sales
- **Adjustment Movements**: Manual inventory corrections

### **Role-based Navigation**

Dynamic sidebar navigation based on user permissions:

- **Admin**: Full access to all modules
- **Manager**: Operations and reporting access
- **Supervisor**: Daily operations focus
- **Clerk**: Specific function access

### **Theme System**

Unified design system with:

- **Theme Cards**: Consistent card layouts
- **Color Schemes**: Professional color palette
- **Responsive Design**: Mobile-optimized interface
- **Dark/Light Modes**: User preference support

## 📚 API Documentation

### **RESTful Endpoints**

#### **Products API**

- `GET /api/products` - List all products
- `POST /api/products` - Create new product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

#### **Inventory API**

- `GET /api/inventory/stock` - Current stock levels
- `POST /api/inventory/transfer` - Location transfer
- `GET /api/inventory/movements` - Movement history

#### **Sales API**

- `POST /api/sales` - Create new sale
- `GET /api/sales/{id}` - Get sale details
- `POST /api/sales/{id}/return` - Process return

## 🛠️ Development

### **Coding Standards**

- PSR-4 autoloading
- PSR-12 coding style
- DocBlock documentation
- Error handling with try-catch
- Database transactions for data integrity

### **Testing**

- Unit tests for models
- Integration tests for controllers
- Browser testing for UI components
- Performance testing for large datasets

### **Contributing**

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 🆘 Support

For support and questions:

- **Documentation**: `/docs` directory
- **Issues**: GitHub Issues tracker
- **Email**: support@hardwarestore.app

## 🔮 Roadmap

### **Upcoming Features**

- **Multi-location Support** - Multiple warehouse locations
- **Advanced Reporting** - Custom report builder
- **Mobile App** - Native mobile application
- **API Expansion** - Complete REST API
- **Integration Hub** - Third-party system integrations
- **Advanced Analytics** - Machine learning insights

### **Version History**

- **v1.0** - Initial release with core functionality
- **v1.1** - Bulk location system implementation
- **v1.2** - Enhanced reporting and analytics
- **v1.3** - Mobile optimization and theme system

---

**Built with ❤️ for hardware store owners and managers**

_This README provides comprehensive documentation for the Hardware Store Management System. For technical support or feature requests, please refer to the support section above._
