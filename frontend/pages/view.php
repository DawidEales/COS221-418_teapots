<?php
// Mock product data
$product = [
    "id" => 1,
    "title" => "Laptop",
    "images" => [ // Array for carousel support
        "main" => "https://via.placeholder.com/300/FF0000/FFFFFF?text=Main"
    ],
    "description" => "A high-performance laptop.",
    "data" => "Brand: Example | Model: X123",
    "prices" => [
        ["retailer" => "Store A", "price" => 999.99],
        ["retailer" => "Store B", "price" => 949.99],
        ["retailer" => "Store C", "price" => 979.99]
    ],
    "best_price" => 949.99,
    "rating" => "⭐⭐⭐⭐"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Product - CompareIt</title>
    <link rel="stylesheet" href="../cssFiles/D.css">
    <style>
        /* Temporary inline styles to ensure layout */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden;
        }
        .main-layout {
            display: flex;
            min-height: calc(100vh - 60px); /* Adjust for header height */
        }
        .carousel-column {
            width: 40%;
            padding: 10px;
            position: relative;
        }
        .details-column {
            width: 60%;
            padding: 10px;
            display: flex;
            flex-direction: column;
        }
        .headingBar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px;
            background: #f8f8f8;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="headingBar">
        <a href="logout.php" class="changeDir">Sign Out</a>
    </div>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Left Section: Carousel (Removed "Laptop" from alt text) -->
        <div class="carousel-column">
            <div class="carousel-container" style="position: relative;">
                <!-- Main Image -->
                <div class="productImageDiv">
                    <input type="image" id="mainImage" src="<?php echo htmlspecialchars($product['images']['main']); ?>" alt="Product Image" class="productImage">
                </div>
                <!-- Carousel Controls -->
                <button class="carousel-control prev" style="position: absolute; top: 50%; left: 0; transform: translateY(-50%);">❮</button>
                <button class="carousel-control next" style="position: absolute; top: 50%; right: 0; transform: translateY(-50%);">❯</button>
            </div>
        </div>

        <!-- Right Section: Details and Bottom Section (Restored "Laptop" title) -->
        <div class="details-column">
            <!-- Details -->
            <div class="contentContainer">
                <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p><?php echo htmlspecialchars($product['data']); ?></p>
                <table class="price-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid black; padding: 5px;">Retailer</th>
                            <th style="border: 1px solid black; padding: 5px;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($product['prices'] as $price): ?>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px;"><?php echo htmlspecialchars($price['retailer']); ?></td>
                                <td style="border: 1px solid black; padding: 5px;">$<?php echo number_format($price['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bottom Section -->
            <div class="bottom-section" style="display: flex; justify-content: space-between; padding: 10px; border-top: 1px solid black;">
                <div style="width: 33%;">
                    <h3>Best Price</h3>
                    <p>$<?php echo number_format($product['best_price'], 2); ?></p>
                </div>
                <div style="width: 33%;">
                    <h3>Rating</h3>
                    <p id="ratingDisplay"><?php echo htmlspecialchars($product['rating']); ?></p>
                    <button id="rateButton">Click to Rate</button>
                </div>
                <div style="width: 33%;">
                    <h3>Reviews</h3>
                    <p>User reviews will go here.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="../pages/jsFiles/viewpage.js"></script>
</body>
</html>