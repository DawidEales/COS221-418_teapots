document.addEventListener('DOMContentLoaded', async () => {
    // Extract product_id from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    const apiKey = localStorage.getItem('api_key'); // Assumes API key is stored after login

    if (!productId) {
        document.querySelector('.contentContainer').innerHTML = '<p>Error: No product ID provided</p>';
        return;
    }

    if (!apiKey) {
        document.querySelector('.contentContainer').innerHTML = '<p>Please log in to view product details</p>';
        return;
    }

    // Fetch product details
    async function fetchProduct() {
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: 'getproduct',
                    api_key: apiKey,
                    product_id: parseInt(productId)
                })
            });
            const data = await response.json();
            if (data.status === 'success') {
                const product = data.data.product;
                const prices = data.data.price_comparisons;

                // Update product details
                document.querySelector('.contentContainer h2').textContent = product.Name || 'Unnamed Product';
                document.querySelector('.contentContainer p:nth-of-type(1)').textContent = product.Description || 'No description available';
                document.querySelector('.contentContainer p:nth-of-type(2)').textContent = `Brand: ${product.BrandName || 'Unknown'} | Category: ${product.CategoryName || 'Unknown'}`;
                
                // Update price table
                const priceTableBody = document.querySelector('.price-table tbody');
                priceTableBody.innerHTML = '';
                for (const retailer in prices) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td style="border: 1px solid black; padding: 5px;">${retailer}</td>
                        <td style="border: 1px solid black; padding: 5px;">$${prices[retailer].discountedPrice}</td>
                    `;
                    priceTableBody.appendChild(row);
                }

                // Update best price
                document.querySelector('.bottom-section div:nth-child(1) p').textContent = `$${product.SalePrice || 'N/A'}`;

                // Update rating
                document.querySelector('#ratingDisplay').textContent = `${product.ReviewAverage || 0} (${product.ReviewCount || 0} reviews)`;

                // Update carousel image
                const mainImage = document.querySelector('#mainImage');
                mainImage.src = product.ThumbnailImage || 'https://via.placeholder.com/300';
                mainImage.alt = product.Name || 'Product Image';

                // Handle carousel images (assuming CarouselImages is a JSON string or comma-separated)
                const carouselImages = product.CarouselImages ? JSON.parse(product.CarouselImages) : [product.ThumbnailImage];
                let currentImageIndex = 0;
                document.querySelector('.carousel-control.prev').addEventListener('click', () => {
                    currentImageIndex = (currentImageIndex - 1 + carouselImages.length) % carouselImages.length;
                    mainImage.src = carouselImages[currentImageIndex] || 'https://via.placeholder.com/300';
                });
                document.querySelector('.carousel-control.next').addEventListener('click', () => {
                    currentImageIndex = (currentImageIndex + 1) % carouselImages.length;
                    mainImage.src = carouselImages[currentImageIndex] || 'https://via.placeholder.com/300';
                });
            } else {
                document.querySelector('.contentContainer').innerHTML = `<p>Error: ${data.message}</p>`;
            }
        } catch (error) {
            console.error('Error fetching product:', error);
            document.querySelector('.contentContainer').innerHTML = '<p>Error loading product details</p>';
        }
    }

    // Fetch reviews
    async function fetchReviews() {
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: 'getreviews',
                    api_key: apiKey,
                    product_id: parseInt(productId)
                })
            });
            const data = await response.json();
            if (data.status === 'success') {
                const reviews = data.data;
                const reviewContainer = document.querySelector('.bottom-section div:nth-child(3) p');
                reviewContainer.innerHTML = reviews.length > 0 ? 
                    reviews.map(review => `
                        <div>
                            <strong>${review.username}</strong> (${review.rating}/5): ${review.title}<br>
                            ${review.description}<br>
                            <small>${new Date(review.timestamp).toLocaleString()}</small>
                        </div>
                    `).join('<hr>') : 'No reviews yet';
            } else {
                console.error('Error fetching reviews:', data.message);
            }
        } catch (error) {
            console.error('Error fetching reviews:', error);
        }
    }

    // Handle rating submission
    document.querySelector('#rateButton').addEventListener('click', () => {
        // Create a modal or form for rating submission
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 20%; left: 50%; transform: translateX(-50%); background: white; padding: 20px; border: 3px solid black; z-index: 10;';
        modal.innerHTML = `
            <h3>Submit Review</h3>
            <form id="reviewForm">
                <label>Rating (1-5):</label><input type="number" id="rating" min="1" max="5" required><br>
                <label>Title:</label><input type="text" id="reviewTitle" required><br>
                <label>Description:</label><textarea id="reviewDescription" required></textarea><br>
                <button type="submit">Submit</button>
                <button type="button" onclick="this.parentElement.parentElement.remove()">Cancel</button>
            </form>
        `;
        document.body.appendChild(modal);

        document.querySelector('#reviewForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const rating = document.querySelector('#rating').value;
            const reviewTitle = document.querySelector('#reviewTitle').value;
            const reviewDescription = document.querySelector('#reviewDescription').value;

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        type: 'addreview',
                        api_key: apiKey,
                        product_id: parseInt(productId),
                        rating: parseInt(rating),
                        review_title: reviewTitle,
                        review_description: reviewDescription
                    })
                });
                const data = await response.json();
                if (data.status === 'success') {
                    modal.remove();
                    fetchReviews(); // Refresh reviews
                    fetchProduct(); // Refresh rating
                    alert('Review submitted successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error submitting review:', error);
                alert('Error submitting review');
            }
        });
    });

    // Initial data fetch
    await Promise.all([fetchProduct(), fetchReviews()]);
});