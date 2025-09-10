# 🏢 MULTI-LOCATION ENTERPRISE SYSTEM
## Complete Implementation Guide

Your hardware store chain needs a sophisticated multi-location system. Here's the complete transformation plan:

## 🎯 **BUSINESS REQUIREMENTS ANALYSIS**

### **Current System Limitations:**
- ❌ Single warehouse design
- ❌ No location-based access control  
- ❌ No multi-store inventory management
- ❌ No location-specific reporting
- ❌ No inter-location transfers

### **Required Multi-Location Features:**
- ✅ **3 Physical Locations**: Kurukshetra, Ambala, Panchkula
- ✅ **Role-Based Access**: Corporate Admin, Location Managers, Store Staff, Warehouse Staff
- ✅ **Location Isolation**: Staff only access assigned locations
- ✅ **Inventory Segregation**: Each location tracks own inventory
- ✅ **Inter-Location Transfers**: Move inventory between stores
- ✅ **Consolidated Reporting**: Corporate view of all locations

## 🏗️ **IMPLEMENTATION STRATEGY**

### **Phase 1: Database Migration (30 minutes)**
```sql
-- Run the migration script
SOURCE create_multi_location_system.sql;
```

### **Phase 2: Code Updates (2-3 hours)**

#### **A. Update Session Management**
Create `app/helpers/location_helpers.php`:
```php
<?php
function getCurrentUserLocation() {
    if (!isset($_SESSION['current_location_id'])) {
        // Set default location based on user assignment
        $db = new Database();
        $db->query("SELECT default_location_id FROM users WHERE user_id = :user_id");
        $db->bind(':user_id', $_SESSION['user_id']);
        $db->execute();
        $result = $db->single();
        $_SESSION['current_location_id'] = $result->default_location_id ?? 1;
    }
    return $_SESSION['current_location_id'];
}

function userCanAccessLocation($userId, $locationId) {
    if (isAdmin()) return true;
    
    $db = new Database();
    $db->query("SELECT COUNT(*) as count FROM user_location_assignments WHERE user_id = :user_id AND location_id = :location_id");
    $db->bind(':user_id', $userId);
    $db->bind(':location_id', $locationId);
    $db->execute();
    return $db->single()->count > 0;
}

function getLocationFilterSQL($column = 'business_location_id') {
    if (isAdmin()) {
        return '1=1'; // No filter for admins
    }
    
    $locationId = getCurrentUserLocation();
    return "$column = $locationId";
}
?>
```

#### **B. Update Inventory Controller**
Modify `app/controllers/InventoryController.php`:
```php
// Add location filter to all inventory queries
public function index() {
    $locationFilter = getLocationFilterSQL('wl.business_location_id');
    
    $this->db->query("
        SELECT i.*, p.product_name, wl.location_name, bl.location_name as store_name
        FROM inventory i
        INNER JOIN products p ON i.product_id = p.product_id
        INNER JOIN warehouse_locations wl ON i.location_id = wl.location_id
        INNER JOIN business_locations bl ON wl.business_location_id = bl.location_id
        WHERE $locationFilter
        ORDER BY p.product_name
    ");
    // ... rest of method
}
```

#### **C. Update Sales Controller** 
Modify `app/controllers/SalesController.php`:
```php
public function create() {
    // Auto-assign current location to sales
    $_POST['business_location_id'] = getCurrentUserLocation();
    // ... rest of method
}
```

#### **D. Location Switcher Component**
Create `app/views/layouts/location_switcher.php`:
```php
<div class="location-switcher dropdown">
    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
        <i class="fas fa-map-marker-alt"></i> 
        <?= $_SESSION['current_location_name'] ?? 'Select Location' ?>
    </button>
    <div class="dropdown-menu">
        <?php
        $locationModel = new BusinessLocation();
        $locations = $locationModel->getUserAccessibleLocations($_SESSION['user_id']);
        foreach ($locations as $location):
        ?>
        <a class="dropdown-item" href="#" onclick="switchLocation(<?= $location->location_id ?>)">
            <i class="fas fa-store"></i> <?= $location->location_name ?>
            <small class="text-muted d-block"><?= $location->city ?></small>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
function switchLocation(locationId) {
    fetch(`<?= URLROOT ?>/locations/switch/${locationId}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'}
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>
```

### **Phase 3: User Interface Updates (1-2 hours)**

#### **A. Enhanced Navigation**
Update `app/views/layouts/header.php`:
```php
<!-- Add location switcher to navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="navbar-nav ml-auto">
        <?php include 'location_switcher.php'; ?>
        <!-- ... existing nav items ... -->
    </div>
</nav>
```

#### **B. Location Dashboard**
Create `app/views/locations/dashboard.php`:
```php
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1><i class="fas fa-building"></i> Multi-Location Dashboard</h1>
        </div>
    </div>
    
    <div class="row">
        <?php foreach ($locations as $location): ?>
        <div class="col-md-4">
            <div class="card location-card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-store"></i> <?= $location->location_name ?></h5>
                    <small><?= $location->city ?></small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h3><?= $locationStats[$location->location_id]->unique_products ?></h3>
                            <small>Products</small>
                        </div>
                        <div class="col-6">
                            <h3>₹<?= number_format($locationStats[$location->location_id]->total_sales) ?></h3>
                            <small>Sales (30d)</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <span class="badge badge-info"><?= $locationStats[$location->location_id]->staff_count ?> Staff</span>
                        </div>
                        <div class="col-6">
                            <span class="badge badge-success"><?= $locationStats[$location->location_id]->storage_locations ?> Locations</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= URLROOT ?>/locations/view/<?= $location->location_id ?>" class="btn btn-primary btn-sm">View Details</a>
                    <a href="#" onclick="switchLocation(<?= $location->location_id ?>)" class="btn btn-outline-primary btn-sm">Switch To</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
```

### **Phase 4: Role-Based Access Control (1 hour)**

#### **A. Enhanced Roles**
Update roles in database:
```sql
INSERT INTO roles (role_name, description, permissions) VALUES
('Corporate Admin', 'Full access to all locations', '{"global": true}'),
('Location Manager', 'Full access to assigned location', '{"location": "full"}'),
('Store Staff', 'Sales and customer service', '{"sales": true, "customers": true}'),
('Warehouse Staff', 'Inventory operations', '{"inventory": true, "receiving": true}');
```

#### **B. Permission Middleware**
Create `app/middleware/LocationAccess.php`:
```php
<?php
class LocationAccess {
    public static function checkAccess($requiredLocationId = null) {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        
        if ($requiredLocationId && !userCanAccessLocation($_SESSION['user_id'], $requiredLocationId)) {
            flash('error_message', 'Access denied to this location');
            redirect('dashboard');
        }
    }
}
?>
```

## 🚀 **DEPLOYMENT STEPS**

### **Step 1: Backup Current System**
```bash
# Backup database
mysqldump -u your_user -p your_database > backup_before_migration.sql

# Backup files
tar -czf webapp_backup.tar.gz /path/to/your/webapp
```

### **Step 2: Run Migration**
```bash
# Navigate to project
cd C:\wamp64\www

# Run migration script
mysql -u root -p your_database < database/migrations/create_multi_location_system.sql
```

### **Step 3: Update Configuration**
```php
// config/app.php
define('MULTI_LOCATION_ENABLED', true);
define('DEFAULT_COMPANY_ID', 1);
define('ENABLE_LOCATION_SWITCHING', true);
```

### **Step 4: Create Initial Data**
```sql
-- Create your staff accounts
INSERT INTO users (username, password_hash, role_id, company_id, default_location_id, full_name, email) VALUES
('kurukshetra_manager', '$2y$10$...', 2, 1, 1, 'Kurukshetra Store Manager', 'manager.krk@yourstore.com'),
('ambala_manager', '$2y$10$...', 2, 1, 2, 'Ambala Store Manager', 'manager.amb@yourstore.com'),
('panchkula_manager', '$2y$10$...', 2, 1, 3, 'Panchkula Store Manager', 'manager.pkl@yourstore.com');

-- Assign managers to locations
INSERT INTO user_location_assignments (user_id, location_id, access_type) VALUES
(2, 1, 'full'), -- Kurukshetra manager
(3, 2, 'full'), -- Ambala manager  
(4, 3, 'full'); -- Panchkula manager
```

## 📊 **BENEFITS AFTER IMPLEMENTATION**

### **For Corporate Admin:**
- 📈 **Consolidated reporting** across all locations
- 👥 **Centralized user management** 
- 📦 **Global inventory visibility**
- 💰 **Cross-location profitability analysis**

### **For Location Managers:**
- 🏪 **Complete control** over assigned location
- 👨‍💼 **Staff management** for location
- 📊 **Location-specific reporting**
- 🔄 **Inter-location transfer requests**

### **For Staff:**
- 🎯 **Focused interface** - only relevant location data
- 🔒 **Secure access** - cannot see other locations
- ⚡ **Faster operations** - location-filtered data
- 📱 **Clear location context** always visible

### **System Benefits:**
- ⚡ **Better Performance** - location-filtered queries
- 🔐 **Enhanced Security** - role-based location access
- 📈 **Scalability** - easy to add new locations
- 🔧 **Maintainability** - organized code structure

## 🎯 **NEXT STEPS**

1. **Review and approve** this implementation plan
2. **Schedule downtime** for migration (recommend weekend)
3. **Train staff** on new multi-location features
4. **Test thoroughly** in development environment first
5. **Gradual rollout** - start with one location

## ⚠️ **IMPORTANT CONSIDERATIONS**

- **Data Migration**: All existing data will be assigned to Kurukshetra location
- **User Training**: Staff will need training on location switching
- **Performance**: Queries are now location-filtered for better performance
- **Backup Strategy**: Regular backups more critical with multi-location data

**Would you like me to proceed with implementing any specific part of this system?** 

I recommend starting with the database migration and testing it with a small subset of data first.
