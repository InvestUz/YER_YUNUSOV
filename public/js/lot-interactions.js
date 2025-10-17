/**
 * LotInteractions Class
 * Handles like/unlike functionality
 */
class LotInteractions {
    constructor(lotId, csrfToken) {
        this.lotId = lotId;
        this.csrfToken = csrfToken;
    }

    async toggleLike() {
        try {
            const response = await fetch(`/lots/${this.lotId}/toggle-like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            const data = await response.json();

            // Update like count
            const likeCount = document.getElementById('likeCount');
            if (likeCount) {
                likeCount.textContent = data.count || 0;
            }

            // Update like icon
            const likeIcon = document.getElementById('likeIcon');
            if (likeIcon) {
                if (data.liked) {
                    likeIcon.setAttribute('fill', 'currentColor');
                    likeIcon.classList.add('fill-red-600', 'text-red-600');
                } else {
                    likeIcon.setAttribute('fill', 'none');
                    likeIcon.classList.remove('fill-red-600', 'text-red-600');
                    likeIcon.classList.add('text-gray-500');
                }
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        }
    }
}

// Global function for inline onclick
function toggleLike() {
    if (window.lotInteractions) window.lotInteractions.toggleLike();
}
