<?php
// Check for product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<p>Error: No product ID provided</p>';
    exit;
}
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
            min-height: calc(100vh - 60px);
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
        <!-- Left Section: Carousel -->
        <div class="carousel-column">
            <div class="carousel-container" style="position: relative;">
                <!-- Main Image -->
                <div class="productImageDiv">
                    <input type="image" id="mainImage" src="https://via.placeholder.com/300" alt="Product Image" class="productImage">
                </div>
                <!-- Carousel Controls -->
                <button class="carousel-control prev" style="position: absolute; top: 50%; left: 0; transform: translateY(-50%);">❮</button>
                <button class="carousel-control next" style="position: absolute; top: 50%; right: 0; transform: translateY(-50%);">❯</button>
            </div>
        </div>

        <!-- Right Section: Details and Bottom Section -->
        <div class="details-column">
            <!-- Details -->
            <div class="contentContainer">
                <h2>Loading...</h2>
                <p>Loading description...</p>
                <p>Loading brand and category...</p>
                <table class="price-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid black; padding: 5px;">Retailer</th>
                            <th style="border: 1px solid black; padding: 5px;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Bottom Section -->
            <div class="bottom-section" style="display: flex; justify-content: space-between; padding: 10px; border-top: 1px solid black;">
                <div style="width: 33%;">
                    <h3>Best Price</h3>
                    <p>Loading...</p>
                </div>
                <div style="width: 33%;">
                    <h3>Rating</h3>
                    <p id="ratingDisplay">Loading...</p>
                    <button id="rateButton">Click to Rate</button>
                </div>
                <div style="width: 33%;">
                    <h3>Reviews</h3>
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="../pages/jsFiles/viewpage.js"></script>
</body>
</html>