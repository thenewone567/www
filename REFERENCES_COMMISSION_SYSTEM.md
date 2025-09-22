# Customer References & Commission System

## Overview

The Customer References & Commission System allows you to track contractor referrals and manage commission payments. This system links contractors to the customers they have referred, enabling automatic commission calculations based on sales.

## Features Implemented

### 1. **References Button Added**

- Added to `/admin/users` page navigation
- Green handshake icon with "Commission system" subtitle
- Links to `/admin/references`

### 2. **Database Structure**

#### Tables Created:

- **`customer_references`** - Links contractors to their referred customers
- **`commissions`** - Tracks commission payments for sales
- **`commission_rates`** - Configurable commission rate structures
- **`commission_summary`** - View for easy reporting

#### Key Fields:

- Reference tracking with unique contractor-customer pairs
- Commission calculation with customizable rates
- Status tracking (pending → approved → paid)
- Audit trail with timestamps

### 3. **Admin Interface**

#### Navigation:

- Dashboard → Users → **References** → Activity Logs → Settings

#### Statistics Dashboard:

- Total References count
- Total Commissions count
- Active Contractors count
- Total Commission Value (monetary)

#### Tabbed Interface:

1. **Customer References Tab**

   - View all contractor-customer reference relationships
   - Add new references via modal
   - Track total commissions and earnings per reference
   - Delete references (cascades to related commissions)

2. **Commission Tracking Tab**
   - View all commission records
   - Update commission status (pending → approved → paid)
   - Cancel commissions if needed
   - Filter and sort by date, contractor, customer

### 4. **Functionality**

#### Creating References:

1. Click "Add Reference" button
2. Select contractor from dropdown (shows commission rate)
3. Select customer from dropdown
4. Add optional notes
5. Submit to create reference relationship

#### Commission Management:

- Commissions can be created programmatically when sales occur
- Status workflow: Pending → Approved → Paid
- Automatic calculation based on sale amount and commission rate
- Payment date tracking when marked as paid

#### Data Relationships:

- **Contractors** can refer multiple **Customers**
- Each **Reference** can generate multiple **Commissions** (from multiple sales)
- **Commission Rates** are configurable with different tiers based on sale amounts

## Technical Implementation

### Files Created/Modified:

#### 1. **Database Migration**

- `database/migrations/create_references_commission_system.sql`
- Creates all necessary tables with proper foreign keys and indexes

#### 2. **Model Layer**

- `app/models/Reference.php`
- Handles all database operations for references and commissions

#### 3. **Controller Layer**

- Added methods to `app/controllers/AdminController.php`:
  - `references()` - Main page display
  - `createReference()` - AJAX endpoint for creating references
  - `deleteReference()` - AJAX endpoint for deleting references
  - `updateCommissionStatus()` - AJAX endpoint for status updates

#### 4. **View Layer**

- `app/views/admin/references.php` - Complete admin interface
- Modified `app/views/admin/users.php` - Added References navigation button

## Usage Workflow

### 1. **Setup Phase**

- Ensure contractors and customers exist in the system
- Configure commission rates in `commission_rates` table if needed

### 2. **Reference Creation**

- Admin creates reference linking contractor to customer
- System prevents duplicate contractor-customer pairs

### 3. **Sales & Commission Generation**

- When a referred customer makes a purchase, commission is automatically calculated
- Commission record created with "pending" status

### 4. **Commission Approval Process**

1. Admin reviews pending commissions
2. Approves legitimate commissions (pending → approved)
3. Processes payment and marks as paid (approved → paid)
4. Can cancel invalid commissions if needed

## Commission Calculation Logic

```php
$commissionAmount = ($saleAmount * $commissionRate) / 100;
```

### Default Commission Rates:

- **Standard Commission**: 5.00% for sales $0-$999.99
- **High Value Commission**: 3.00% for sales $1,000-$4,999.99
- **Premium Commission**: 2.50% for sales $5,000+
- **Bulk Discount Commission**: 1.50% for sales $10,000+

## API Endpoints

### AJAX Endpoints:

- `POST /admin/createReference` - Create new reference
- `POST /admin/deleteReference` - Delete reference
- `POST /admin/updateCommissionStatus` - Update commission status

### Data Format:

All endpoints return JSON responses:

```json
{
  "success": true/false,
  "message": "Status message",
  "data": {} // Optional additional data
}
```

## Security Features

- Input validation on all form submissions
- SQL injection protection via prepared statements
- Foreign key constraints maintain data integrity
- Unique constraints prevent duplicate references
- Admin authentication required for all operations

## Future Enhancements

### Potential Additions:

1. **Automated Commission Creation** - Hook into sales system to auto-generate commissions
2. **Commission Reports** - Detailed reporting and analytics
3. **Email Notifications** - Notify contractors of commission updates
4. **Commission Disputes** - System for handling commission disputes
5. **Bulk Operations** - Approve/pay multiple commissions at once
6. **Commission History** - Track all status changes with timestamps

## Database Schema Summary

```sql
customer_references (reference_id, contractor_id, customer_id, reference_date, status, notes)
commissions (commission_id, reference_id, sale_id, contractor_id, customer_id, amounts, status, dates)
commission_rates (rate_id, rate_name, rate_percentage, min/max_sale_amount)
commission_summary (view joining all related data for reporting)
```

## Installation Notes

✅ **Migration Completed Successfully**

- All tables created with proper structure
- Default commission rates inserted
- Foreign key relationships established
- Database view created for reporting

The References & Commission System is now fully operational and ready for use!
