# Hardware Store Management System - Quick Start

## 🎯 What This System Does

A complete business management solution for hardware stores with:

- **Point of Sale (POS)** - Process sales transactions
- **Inventory Management** - Track stock levels and locations
- **Purchase Management** - Handle supplier orders and receiving
- **Reporting** - Business analytics and insights
- **User Management** - Role-based access control

## 🚀 Key Features

### **Sales Module**

- Complete POS system with barcode scanning
- Customer management and history
- Multiple payment methods
- Invoice generation and printing
- Return/refund processing

### **Inventory Module**

- Real-time stock tracking across multiple locations
- **Bulk receiving workflow** - Items go to B-001, B-002 staging areas first
- **Location transfers** - Move items from bulk to sales floor
- Low stock alerts and reorder notifications
- Complete movement audit trail

### **Purchase Module**

- Purchase order creation and management
- Supplier database and performance tracking
- **Systematic receiving process** with bulk location assignment
- Three-way matching (PO, Receipt, Invoice)

### **Warehouse Operations**

- **Bulk Locations (B-001 to B-010)** - Receiving dock staging areas
- **Regular Locations** - Sales floor storage (A-001, C-001, etc.)
- **Transfer Interface** - Move items between locations
- **FIFO Logic** - First-in-first-out stock rotation

### **User Roles**

- **Admin** - Full system access
- **Manager** - Operations and reporting
- **Supervisor** - Daily operations and stock management
- **Sales Clerk** - POS and customer service
- **Stock Clerk** - Inventory and receiving focus

## 📋 Quick Installation

1. **Setup Database**

   ```bash
   mysql -u root -p < database/migrations/database.sql
   mysql -u root -p < database/migrations/create_bulk_locations_updated.sql
   ```

2. **Configure Database Connection**

   ```php
   // config/database.php
   define('DB_NAME', 'master_hardware');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Default Login**
   - Username: `admin`
   - Password: `admin123`

## 🔄 Business Workflow

### **Receiving Process**

```
Truck Arrives → Items Received → Bulk Location (B-001) → Quality Check → Transfer to Sales Floor (A-001)
```

### **Sales Process**

```
Customer → POS Scan → Stock Check → Payment → Inventory Deduction → Receipt
```

### **Inventory Flow**

```
Bulk Locations (B-001, B-002) → Transfer Interface → Regular Locations (A-001, C-001) → Sales Floor
```

## 🎨 User Interface

- **Modern Dashboard** - Real-time KPIs and charts
- **Responsive Design** - Works on desktop, tablet, mobile
- **Theme System** - Consistent design across all pages
- **Role-based Navigation** - Sidebar adapts to user permissions

## 📊 Reporting & Analytics

- **Sales Reports** - Daily, weekly, monthly performance
- **Inventory Reports** - Stock levels, valuation, movement history
- **Purchase Analytics** - Vendor performance and costs
- **Financial Reports** - Revenue, expenses, profit analysis

## 🔐 Security Features

- Role-based access control
- Secure password authentication
- Activity logging for audit trails
- Data validation and sanitization
- Database transaction safety

## 📱 System Requirements

- **PHP 7.4+** with PDO, GD extensions
- **MySQL 8.0+** database server
- **Apache/Nginx** web server
- **Modern browser** (Chrome, Firefox, Safari, Edge)

---

## 🆘 Need Help?

- Check the full [README.md](README.md) for complete documentation
- Review `/docs` folder for detailed guides
- Default admin login: `admin` / `admin123`

**This system handles the complete hardware store workflow from receiving goods to selling them to customers!** 🏪
