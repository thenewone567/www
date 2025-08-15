<?php
class Barcode
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getBarcodes()
    {
        $this->db->query("SELECT * FROM barcode");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addBarcode($data)
    {
        $this->db->query("INSERT INTO barcode (product_id, barcode_value, type) VALUES (:product_id, :barcode_value, :type)");
        // Bind values
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':barcode_value', $data['barcode_value']);
        $this->db->bind(':type', $data['type']);

        // Execute
        return $this->db->execute();
    }

    public function getBarcodeByValue($value)
    {
        $this->db->query("SELECT * FROM barcode WHERE barcode_value = :value");
        $this->db->bind(':value', $value);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    /**
     * Get product information by barcode value
     */
    public function getProductByBarcode($barcodeValue)
    {
        try {
            $this->db->query("
                SELECT p.*, b.barcode_value, b.type as barcode_type,
                       c.category_name, br.brand_name, u.unit_name
                FROM barcode b
                INNER JOIN products p ON b.product_id = p.product_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands br ON p.brand_id = br.brand_id
                LEFT JOIN units u ON p.unit_id = u.unit_id
                WHERE b.barcode_value = :barcode_value 
                AND b.is_active = 1 
                AND p.is_active = 1
            ");
            $this->db->bind(':barcode_value', $barcodeValue);

            $result = $this->db->single();
            return $result ? $result : null;
        } catch (Exception $e) {
            error_log("Error in getProductByBarcode: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate barcode for product
     */
    public function generateBarcodeForProduct($productId, $type = 'CODE128')
    {
        try {
            // Generate unique barcode value
            $barcodeValue = $this->generateUniqueBarcode();

            $this->db->query("
                INSERT INTO barcode (product_id, barcode_value, type, is_active) 
                VALUES (:product_id, :barcode_value, :type, 1)
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':barcode_value', $barcodeValue);
            $this->db->bind(':type', $type);

            if ($this->db->execute()) {
                return $barcodeValue;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in generateBarcodeForProduct: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate unique barcode value
     */
    private function generateUniqueBarcode()
    {
        do {
            // Generate 12-digit barcode
            $barcode = str_pad(mt_rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);

            // Check if already exists
            $this->db->query("SELECT barcode_id FROM barcode WHERE barcode_value = :barcode");
            $this->db->bind(':barcode', $barcode);
            $exists = $this->db->single();
        } while ($exists);

        return $barcode;
    }

    /**
     * Get barcodes for a specific product
     */
    public function getBarcodesForProduct($productId)
    {
        try {
            $this->db->query("
                SELECT * FROM barcode 
                WHERE product_id = :product_id 
                AND is_active = 1 
                ORDER BY barcode_id DESC
            ");
            $this->db->bind(':product_id', $productId);

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getBarcodesForProduct: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate barcode image as base64
     */
    public function generateBarcodeImage($barcodeValue, $type = 'CODE128')
    {
        try {
            require_once APPROOT . DS . 'vendor' . DS . 'autoload.php';
            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

            $barcodeType = $generator::TYPE_CODE_128;
            if ($type === 'CODE39') {
                $barcodeType = $generator::TYPE_CODE_39;
            } elseif ($type === 'EAN13') {
                $barcodeType = $generator::TYPE_EAN_13;
            }

            return base64_encode($generator->getBarcode($barcodeValue, $barcodeType));
        } catch (Exception $e) {
            error_log("Error generating barcode image: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate barcode for warehouse location
     */
    public function generateBarcodeForLocation($locationId, $type = 'CODE128')
    {
        try {
            // Generate unique barcode value for location
            $barcodeValue = $this->generateUniqueLocationBarcode();

            $this->db->query("
                INSERT INTO location_barcodes (location_id, barcode_value, type, is_active) 
                VALUES (:location_id, :barcode_value, :type, 1)
            ");
            $this->db->bind(':location_id', $locationId);
            $this->db->bind(':barcode_value', $barcodeValue);
            $this->db->bind(':type', $type);

            if ($this->db->execute()) {
                return $barcodeValue;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in generateBarcodeForLocation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate unique location barcode value
     */
    private function generateUniqueLocationBarcode()
    {
        do {
            // Generate location barcode with prefix "LOC"
            $barcode = 'LOC' . str_pad(mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);

            // Check if already exists
            $this->db->query("SELECT barcode_id FROM location_barcodes WHERE barcode_value = :barcode");
            $this->db->bind(':barcode', $barcode);
            $exists = $this->db->single();
        } while ($exists);

        return $barcode;
    }

    /**
     * Get location information by barcode value
     */
    public function getLocationByBarcode($barcodeValue)
    {
        try {
            $this->db->query("
                SELECT l.*, lb.barcode_value, lb.type as barcode_type
                FROM location_barcodes lb
                INNER JOIN locations l ON lb.location_id = l.id
                WHERE lb.barcode_value = :barcode_value 
                AND lb.is_active = 1
            ");
            $this->db->bind(':barcode_value', $barcodeValue);

            $result = $this->db->single();
            return $result ? $result : null;
        } catch (Exception $e) {
            error_log("Error in getLocationByBarcode: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get barcodes for a specific location
     */
    public function getBarcodesForLocation($locationId)
    {
        try {
            $this->db->query("
                SELECT * FROM location_barcodes 
                WHERE location_id = :location_id 
                AND is_active = 1 
                ORDER BY barcode_id DESC
            ");
            $this->db->bind(':location_id', $locationId);

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getBarcodesForLocation: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all location barcodes
     */
    public function getAllLocationBarcodes()
    {
        try {
            $this->db->query("
                SELECT lb.*, l.name as location_name, l.standardized_address, l.description
                FROM location_barcodes lb
                INNER JOIN locations l ON lb.location_id = l.id
                WHERE lb.is_active = 1
                ORDER BY l.name ASC
            ");

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getAllLocationBarcodes: " . $e->getMessage());
            return [];
        }
    }
}
