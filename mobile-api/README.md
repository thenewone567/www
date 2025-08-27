# Mobile API Documentation

## Overview

This API provides backend services for the Hardware Store mobile applications (Customer and Contractor apps).

## Base URL

```
http://localhost/mobile-api/
```

## Authentication

All API endpoints (except login) require JWT token authentication.

### Headers Required:

```
Authorization: Bearer <jwt_token>
Content-Type: application/json
```

## Endpoints

### Authentication Endpoints

#### POST /mobile-api/auth.php

Login for both customers and contractors.

**Customer Login:**

```json
{
  "action": "customer_login",
  "email": "customer@example.com",
  "password": "password123"
}
```

**Contractor Login:**

```json
{
  "action": "contractor_login",
  "email": "contractor@example.com",
  "password": "password123"
}
```

**Token Validation:**

```json
{
  "action": "validate_token",
  "token": "your-jwt-token"
}
```

**Response:**

```json
{
  "success": true,
  "token": "jwt-token-here",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "type": "customer"
  }
}
```

### Customer Endpoints

#### GET /mobile-api/customer/dashboard

Get customer dashboard data including stats and profile.

**Response:**

```json
{
  "success": true,
  "data": {
    "customer": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "123-456-7890",
      "status": "active"
    },
    "stats": {
      "active_orders": 2,
      "completed_orders": 15,
      "pending_orders": 1,
      "total_spent": 1250.0
    }
  }
}
```

#### GET /mobile-api/customer/profile

Get customer profile information.

#### PUT /mobile-api/customer/profile

Update customer profile.

**Request:**

```json
{
  "name": "John Doe",
  "phone": "123-456-7890",
  "address": "123 Main St, City, State",
  "contact_info": {
    "preferred_contact": "email",
    "notifications": true
  }
}
```

#### GET /mobile-api/customer/orders

Get customer orders (to be implemented with order system).

### Contractor Endpoints

#### GET /mobile-api/contractor/dashboard

Get contractor dashboard data including stats and profile.

**Response:**

```json
{
  "success": true,
  "data": {
    "contractor": {
      "id": 1,
      "name": "Bob Builder",
      "email": "bob@example.com",
      "phone": "123-456-7890",
      "status": "active",
      "skills": "Plumbing, Electrical",
      "hourly_rate": 75.0
    },
    "stats": {
      "active_jobs": 3,
      "completed_jobs": 42,
      "earnings_this_month": 3200.0,
      "total_earnings": 25000.0
    }
  }
}
```

#### GET /mobile-api/contractor/profile

Get contractor profile information.

#### PUT /mobile-api/contractor/profile

Update contractor profile.

**Request:**

```json
{
  "name": "Bob Builder",
  "phone": "123-456-7890",
  "address": "456 Oak St, City, State",
  "skills": "Plumbing, Electrical, HVAC",
  "experience": "10 years",
  "hourly_rate": 80.0,
  "availability": "Mon-Fri 8AM-6PM",
  "certifications": "Licensed Electrician, Certified Plumber"
}
```

#### GET /mobile-api/contractor/jobs

Get contractor jobs (to be implemented with job system).

#### GET /mobile-api/contractor/earnings

Get contractor earnings data (to be implemented with payment system).

## Error Responses

```json
{
  "success": false,
  "message": "Error description"
}
```

Common error codes:

- **401 Unauthorized**: Invalid or missing token
- **404 Not Found**: Endpoint doesn't exist
- **400 Bad Request**: Invalid request data
- **500 Server Error**: Internal server error

## Mobile App Integration

### React Native Example:

```javascript
// Login
const loginUser = async (email, password, userType) => {
  const response = await fetch("http://localhost/mobile-api/auth.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: `${userType}_login`,
      email,
      password,
    }),
  });

  const result = await response.json();

  if (result.success) {
    // Store token in AsyncStorage
    await AsyncStorage.setItem("auth_token", result.token);
    await AsyncStorage.setItem("user_type", userType);
  }

  return result;
};

// Authenticated API call
const makeAuthenticatedRequest = async (endpoint) => {
  const token = await AsyncStorage.getItem("auth_token");

  const response = await fetch(`http://localhost/mobile-api/${endpoint}`, {
    method: "GET",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  return await response.json();
};
```

### Flutter Example:

```dart
// Login
Future<Map<String, dynamic>> loginUser(String email, String password, String userType) async {
    final response = await http.post(
        Uri.parse('http://localhost/mobile-api/auth.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
            'action': '${userType}_login',
            'email': email,
            'password': password,
        }),
    );

    final result = jsonDecode(response.body);

    if (result['success']) {
        // Store token in SharedPreferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', result['token']);
        await prefs.setString('user_type', userType);
    }

    return result;
}

// Authenticated API call
Future<Map<String, dynamic>> makeAuthenticatedRequest(String endpoint) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');

    final response = await http.get(
        Uri.parse('http://localhost/mobile-api/$endpoint'),
        headers: {
            'Authorization': 'Bearer $token',
            'Content-Type': 'application/json',
        },
    );

    return jsonDecode(response.body);
}
```

## Next Steps

1. **Integrate with existing order/job systems** - Connect the placeholder endpoints with your actual business logic
2. **Add push notifications** - Implement Firebase/OneSignal for mobile notifications
3. **Add file upload endpoints** - For profile pictures, job photos, etc.
4. **Implement real-time features** - WebSocket connections for live updates
5. **Add payment integration** - Stripe/PayPal for mobile payments
6. **Enhance security** - Add rate limiting, input validation, SQL injection protection

## Mobile App Development

Choose your preferred technology:

- **React Native** - JavaScript, cross-platform
- **Flutter** - Dart, cross-platform
- **Native** - Java/Kotlin (Android) + Swift (iOS)

The API is framework-agnostic and will work with any mobile technology that can make HTTP requests.
