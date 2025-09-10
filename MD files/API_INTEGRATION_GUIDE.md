# Hardware Store API Integration Guide

## System Information

- **Base URL**: `http://localhost` (Port 80)
- **API Directory**: `/api/`
- **Mobile API**: `/mobile-api/`
- **CORS**: Enabled for all origins (\*)

## Available API Endpoints

### Products

- `GET /api/getProducts.php` - Get all products or single product by ID
- `GET /api/getProducts.php?id=123` - Get specific product

### Suppliers

- `GET /api/getSuppliers.php` - Get all suppliers
- `GET /api/getSuppliers.php?id=123` - Get specific supplier

### Categories & Brands

- `GET /api/getCategories.php` - Get all categories
- `GET /api/getBrands.php` - Get all brands
- `GET /api/getUnits.php` - Get all units

### Purchase Orders

- `GET /api/getPODetails.php` - Get purchase order details
- `GET /api/searchPurchaseOrder.php` - Search purchase orders
- `GET /api/getAvailablePOs.php` - Get available purchase orders

### Customers & Locations

- `GET /api/getCustomers.php` - Get all customers
- `GET /api/getLocations.php` - Get all locations
- `GET /api/getDockLocations.php` - Get dock locations

## Integration Examples

### 1. PHP Integration

```php
function callAPI($endpoint, $params = []) {
    $url = 'http://localhost/api/' . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Usage
$products = callAPI('getProducts.php');
$suppliers = callAPI('getSuppliers.php');
```

### 2. JavaScript Integration

```javascript
async function callAPI(endpoint, params = {}) {
  const url = new URL("http://localhost/api/" + endpoint);
  Object.keys(params).forEach((key) => {
    url.searchParams.append(key, params[key]);
  });

  const response = await fetch(url);
  return await response.json();
}

// Usage
const products = await callAPI("getProducts.php");
const suppliers = await callAPI("getSuppliers.php");
```

### 3. Python Integration

```python
import requests

def call_api(endpoint, params=None):
    url = f'http://localhost/api/{endpoint}'
    response = requests.get(url, params=params)
    return response.json()

# Usage
products = call_api('getProducts.php')
suppliers = call_api('getSuppliers.php')
```

## Security Notes

- All API endpoints currently allow CORS from any origin
- No authentication required for basic endpoints
- For production, consider adding API keys or JWT authentication
- Database credentials are in `app/config.php`

## Testing Your Integration

1. Test with a simple endpoint first: `http://localhost/api/getCategories.php`
2. Check the response format (JSON)
3. Ensure your application can handle the data structure
4. Test error scenarios (invalid IDs, network issues)

## Network Access

- If your other app is on a different machine, use your local IP instead of localhost
- Example: `http://192.168.1.100/api/getProducts.php` (replace with your actual IP)
- Make sure port 80 is accessible through your firewall
