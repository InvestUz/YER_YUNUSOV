/**
 * LotGallery Class
 * Handles image gallery navigation and display
 */
class LotGallery {
    constructor(images) {
        this.images = images || [];
        this.currentIndex = 0;

        if (this.images.length > 0) {
            this.init();
        }
    }

    init() {
        this.updateImage();

        // Keyboard navigation
        if (this.images.length > 1) {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.previousImage();
                if (e.key === 'ArrowRight') this.nextImage();
            });
        }
    }

    showImage(index) {
        if (index < 0 || index >= this.images.length) return;

        this.currentIndex = index;
        this.updateImage();
    }

    previousImage() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.updateImage();
    }

    nextImage() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.updateImage();
    }

    updateImage() {
        const mainImage = document.getElementById('mainImage');
        const imageCounter = document.getElementById('currentImageIndex');

        if (mainImage && this.images.length > 0) {
            // Fade effect
            mainImage.style.opacity = '0.5';

            setTimeout(() => {
                mainImage.src = this.images[this.currentIndex];
                mainImage.style.opacity = '1';
            }, 150);
        }

        // Update counter
        if (imageCounter) {
            imageCounter.textContent = this.currentIndex + 1;
        }

        // Update thumbnails
        document.querySelectorAll('[id^="thumb-"]').forEach((thumb, index) => {
            if (index === this.currentIndex) {
                thumb.classList.add('border-blue-600', 'ring-2', 'ring-blue-200');
                thumb.classList.remove('border-gray-300');
            } else {
                thumb.classList.remove('border-blue-600', 'ring-2', 'ring-blue-200');
                thumb.classList.add('border-gray-300');
            }
        });
    }
}

// Global functions for inline onclick handlers
function showImage(index) {
    if (window.lotGallery) window.lotGallery.showImage(index);
}

function previousImage() {
    if (window.lotGallery) window.lotGallery.previousImage();
}

function nextImage() {
    if (window.lotGallery) window.lotGallery.nextImage();
}
