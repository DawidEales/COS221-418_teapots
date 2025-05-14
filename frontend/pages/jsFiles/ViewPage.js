//### viewpage.js

let rateButton;
let thumbnail;
let mainImage;

document.addEventListener("DOMContentLoaded", initialize);

function initialize() {
    rateButton = document.getElementById("rateButton");
    thumbnail = document.getElementById("thumbnail");
    mainImage = document.getElementById("mainImage");

    //### Add event listener for rating button
    if (rateButton) {
        rateButton.addEventListener("click", showRatingForm);
    }

    //### Add event listener for thumbnail (image gallery)
    if (thumbnail && mainImage) {
        thumbnail.addEventListener("click", () => {
            mainImage.src = thumbnail.src; // Swap image (placeholder logic)
        });
    }
}

//### Show a simple rating form (placeholder for now)
function showRatingForm() {
    const rating = prompt("Enter your rating (1-5 stars):");
    if (rating && rating >= 1 && rating <= 5) {
        const stars = "⭐".repeat(rating) + "☆".repeat(5 - rating);
        document.getElementById("ratingDisplay").innerText = stars;
        //### Later: Send rating to backend via API
    } else {
        alert("Please enter a valid rating between 1 and 5.");
    }
}
