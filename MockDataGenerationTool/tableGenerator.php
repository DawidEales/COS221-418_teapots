<?php
//Creates table in the database for a user inputted company name,
// the prices are then randomly adjusted and discounts applied based on parameters
include_once 'connection.php';

// Function to get user input for table name and other parameters
function getInputParameters() {
    $params = [
        'tableName' => 'TestCompany',
        'productPercentage' => 75,
        'priceMin' => 20,
        'priceMax' => 20,
        'discountMin' => 0,
        'discountMax' => 40,
        'discountedProductPercentage' => 100
    ];

    if (php_sapi_name() === 'cli') {
        echo "Enter company name for the new table: ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        $params['tableName'] = trim($line);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $params['tableName'] = isset($_POST['tableName']) ? trim($_POST['tableName']) : $params['tableName'];
        $params['productPercentage'] = isset($_POST['productPercentage']) ? (int)$_POST['productPercentage'] : $params['productPercentage'];
        $params['priceMin'] = isset($_POST['priceMin']) ? (int)$_POST['priceMin'] : $params['priceMin'];
        $params['priceMax'] = isset($_POST['priceMax']) ? (int)$_POST['priceMax'] : $params['priceMax'];
        $params['discountMin'] = isset($_POST['discountMin']) ? (int)$_POST['discountMin'] : $params['discountMin'];
        $params['discountMax'] = isset($_POST['discountMax']) ? (int)$_POST['discountMax'] : $params['discountMax'];
        $params['discountedProductPercentage'] = isset($_POST['discountedProductPercentage']) ?
            (int)$_POST['discountedProductPercentage'] : $params['discountedProductPercentage'];
    }

    return $params;
}

$conn = getConnection();
$params = getInputParameters();

$tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $params['tableName']);
$productPercentage = $params['productPercentage'];
$priceMin = $params['priceMin'];
$priceMax = $params['priceMax'];
$discountMin = $params['discountMin'];
$discountMax = $params['discountMax'];
$discountedProductPercentage = $params['discountedProductPercentage'];

$prodTableName = "Products";
$productID = "ProductID";
$initialPrice = "initial_price";
$finalPrice = "final_price";

//Select all the products
$query = "SELECT $productID, $initialPrice, $finalPrice FROM $prodTableName";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

//pick a selection of the total products based on percentage
$numProducts = mysqli_num_rows($result);
$targetCount = (int)($numProducts * ($productPercentage / 100));

$allProducts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $allProducts[] = $row;
}

// Randomly select products and check for duplicates
$randomProducts = [];
$selectedProductIds = [];
shuffle($allProducts);

foreach ($allProducts as $product) {
    if (!in_array($product[$productID], $selectedProductIds)) {
        $selectedProductIds[] = $product[$productID];
        $randomProducts[] = $product;

        if (count($randomProducts) >= $targetCount) {
            break;
        }
    }
}

// Calculate how many products should receive a discount
$numProductsToDiscount = ceil(count($randomProducts) * ($discountedProductPercentage / 100));

// Process each product
for ($i = 0; $i < count($randomProducts); $i++) {
    // Adjust initial price for all products
    $adjustmentFactor = (mt_rand(-$priceMin, $priceMax) / 100) + 1;
    $randomProducts[$i]['adjusted_initial'] = round($randomProducts[$i][$initialPrice] * $adjustmentFactor, 2);

    // Apply discount only to the percentage of products that should get discounts
    if ($i < $numProductsToDiscount) {
        $discountPercentage = mt_rand($discountMin, $discountMax);
    } else {
        $discountPercentage = 0; // No discount
    }

    $randomProducts[$i]['discount_pct'] = $discountPercentage;
    $randomProducts[$i]['final_adjusted'] = round($randomProducts[$i]['adjusted_initial'] * (1 - $discountPercentage/100), 2);
}

//Create the new table and insert the new products array into it
$createTableQuery = "CREATE TABLE IF NOT EXISTS $tableName (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    original_initial_price DECIMAL(10, 2) NOT NULL,
    adjusted_initial_price DECIMAL(10, 2) NOT NULL,
    discount_percentage INT NOT NULL,
    final_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES $prodTableName($productID)
)";

if (!mysqli_query($conn, $createTableQuery)) {
    die("Error creating table: " . mysqli_error($conn));
}

// Insert products into the new table
$insertQuery = "INSERT INTO $tableName (product_id, original_initial_price, adjusted_initial_price, discount_percentage, final_price) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $insertQuery);

if (!$stmt) {
    die("Error preparing statement: " . mysqli_error($conn));
}

// Insert each product
foreach ($randomProducts as $product) {
    mysqli_stmt_bind_param($stmt, "iddid",
        $product[$productID],
        $product[$initialPrice],
        $product['adjusted_initial'],
        $product['discount_pct'],
        $product['final_adjusted']
    );

    if (!mysqli_stmt_execute($stmt)) {
        echo "Error inserting product " . $product[$productID] . ": " . mysqli_stmt_error($stmt) . "\n";
    }
}

mysqli_stmt_close($stmt);
echo "Successfully created table '$tableName' with " . count($randomProducts) . " products using the following parameters:<br>";
echo "- Product percentage: $productPercentage%<br>";
echo "- Price adjustment range: -$priceMin% to +$priceMax%<br>";
echo "- Percentage of products discounted: $discountedProductPercentage%<br>";
echo "- Discount range: $discountMin% to $discountMax%";

mysqli_close($conn);
?>