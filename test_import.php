<?php
/**
 * Test script to verify import functionality
 * Run this from the PrestaShop root directory
 */

// Suppress headers warning
ob_start();

require_once 'config/config.inc.php';

if (!defined('_PS_VERSION_')) {
    exit('This script must be run from PrestaShop root directory');
}

// Load DynamicProduct classes
require_once _PS_MODULE_DIR_ . 'dynamicproduct/dynamicproduct.php';
require_once _PS_MODULE_DIR_ . 'dynamicproduct/classes/module/DynamicHandler.php';

// Test product ID - change this to an existing product
$test_product_id = 115; // Product "Rolety dzień i noc"

try {
    // Load the module
    $module = Module::getInstanceByName('dynamicproduct');
    if (!$module || !$module->active) {
        die("DynamicProduct module not found or not active\n");
    }

    // Create handler
    $context = Context::getContext();
    $handler = new \DynamicProduct\classes\module\DynamicHandler($module, $context);

    // Test export
    echo "Testing export...\n";
    $export_data = $handler->exportConfig($test_product_id, false);
    echo "Export successful. Data keys: " . implode(', ', array_keys($export_data)) . "\n";

    // Check if hidden_fields exists in export
    if (isset($export_data['hidden_fields'])) {
        echo "✓ hidden_fields found in export\n";
    } else {
        echo "✗ hidden_fields NOT found in export\n";
    }

    // Test import to a new product (create a test product first)
    echo "\nTesting import...\n";

    // Create a test product for import
    $test_product = new Product();
    $test_product->name = [1 => 'Test Import Product'];
    $test_product->reference = 'TEST-IMPORT-' . time();
    $test_product->active = 0; // Make it inactive so it doesn't show in shop
    $test_product->id_category_default = 2; // Home category
    $test_product->price = 100;
    $test_product->add();

    $new_product_id = $test_product->id;
    echo "Created test product ID: $new_product_id\n";

    // Perform import
    $handler->importConfig($new_product_id, $export_data);
    echo "Import completed successfully\n";

    // Verify import results
    echo "\nVerifying import results...\n";

    // Check field groups
    $field_groups = \DynamicProduct\classes\models\DynamicProductFieldGroup::getByIdProduct($new_product_id);
    echo "Field groups imported: " . count($field_groups) . "\n";

    // Check fields
    $fields = \DynamicProduct\classes\models\DynamicField::getFieldsByIdProduct($new_product_id);
    echo "Fields imported: " . count($fields) . "\n";

    // Check steps
    $steps = \DynamicProduct\classes\models\DynamicProductStep::getByIdProduct($new_product_id);
    echo "Steps imported: " . count($steps) . "\n";

    // Check visibility settings
    $visibility_count = \Db::getInstance()->getValue(
        'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'dynamicproduct_visibility WHERE id_product = ' . (int)$new_product_id
    );
    echo "Visibility settings imported: $visibility_count\n";

    // Clean up test product
    echo "\nCleaning up test product...\n";
    $handler->clearConfig($new_product_id);
    if ($test_product->id) {
        $test_product->delete();
    }

    echo "Test completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
