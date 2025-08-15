<?php
class CartSessionManager
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Clear all cart related session data
     */
    public function clearAllCartData()
    {
        $cartKeys = ['cart', 'purchase_cart', 'shopping_cart', 'order_cart', 'temp_cart'];

        foreach ($cartKeys as $key) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }

        // Also clear any cart-related flash messages
        $flashKeys = ['cart_message', 'purchase_message', 'order_message'];
        foreach ($flashKeys as $key) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }

        return true;
    }

    /**
     * Get cart contents
     */
    public function getCart()
    {
        return $_SESSION['purchase_cart'] ?? [];
    }

    /**
     * Set cart contents
     */
    public function setCart($cartData)
    {
        $_SESSION['purchase_cart'] = $cartData;
    }

    /**
     * Add item to cart
     */
    public function addToCart($productId, $quantity = 1, $price = 0, $supplierId = null)
    {
        if (!isset($_SESSION['purchase_cart'])) {
            $_SESSION['purchase_cart'] = [];
        }

        $cartKey = $productId . '_' . ($supplierId ?? 'no_supplier');

        if (isset($_SESSION['purchase_cart'][$cartKey])) {
            $_SESSION['purchase_cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['purchase_cart'][$cartKey] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'supplier_id' => $supplierId
            ];
        }

        return true;
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($productId, $supplierId = null)
    {
        $cartKey = $productId . '_' . ($supplierId ?? 'no_supplier');

        if (isset($_SESSION['purchase_cart'][$cartKey])) {
            unset($_SESSION['purchase_cart'][$cartKey]);
            return true;
        }

        return false;
    }

    /**
     * Get cart total
     */
    public function getCartTotal()
    {
        $total = 0;
        if (isset($_SESSION['purchase_cart'])) {
            foreach ($_SESSION['purchase_cart'] as $item) {
                $total += $item['quantity'] * $item['price'];
            }
        }
        return $total;
    }

    /**
     * Get cart item count
     */
    public function getCartItemCount()
    {
        return isset($_SESSION['purchase_cart']) ? count($_SESSION['purchase_cart']) : 0;
    }

    /**
     * Check if cart is empty
     */
    public function isCartEmpty()
    {
        return $this->getCartItemCount() === 0;
    }
}
?>