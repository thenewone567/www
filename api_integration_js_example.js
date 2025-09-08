// JavaScript example for calling your API from a web app

// Function to call Hardware Store API
async function callHardwareStoreAPI(endpoint, params = {}) {
    const baseUrl = 'http://localhost/api/'; // Your system's URL
    const url = new URL(baseUrl + endpoint);

    // Add query parameters
    Object.keys(params).forEach(key => {
        url.searchParams.append(key, params[key]);
    });

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('API call failed:', error);
        throw error;
    }
}

// Example usage:
async function loadProducts() {
    try {
        const products = await callHardwareStoreAPI('getProducts.php');
        console.log('Products loaded:', products);

        // Update your UI with the products
        displayProducts(products);
    } catch (error) {
        console.error('Failed to load products:', error);
    }
}

// Load suppliers
async function loadSuppliers() {
    try {
        const suppliers = await callHardwareStoreAPI('getSuppliers.php');
        console.log('Suppliers loaded:', suppliers);
        return suppliers;
    } catch (error) {
        console.error('Failed to load suppliers:', error);
    }
}

// Example with parameters
async function getProductById(productId) {
    try {
        const product = await callHardwareStoreAPI('getProducts.php', { id: productId });
        return product;
    } catch (error) {
        console.error('Failed to load product:', error);
    }
}
