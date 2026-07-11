/**
 * Custom Blog Pro - Frontend JS
 */

document.addEventListener('DOMContentLoaded', () => {

    /**
     * Reading Progress Bar
     */
    const progressBar = document.getElementById('cbp-reading-progress');
    if (progressBar) {
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + '%';
        });
    }

    /**
     * AJAX Pagination & Filtering Skeleton
     * To be implemented fully in M3.4
     */
    const ajaxContainer = document.getElementById('cbp-ajax-container');
    if (ajaxContainer) {
        // Logic for handling "Load More" and taxonomy filter clicks
        // will fetch from WP_AJAX endpoint.
    }

    /**
     * Ad View and Click Tracking (Module 5)
     */
    if (typeof cbpSettings !== 'undefined' && cbpSettings.rest_url) {
        const adWrappers = document.querySelectorAll('.cbp-ad-wrapper');
        
        if (adWrappers.length > 0) {
            
            // 1. View Tracking via IntersectionObserver
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.5 // 50% of the ad must be visible
            };
            
            const adObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const ad = entry.target;
                        const adId = ad.dataset.adId;
                        const postId = ad.dataset.postId;
                        
                        // Send View request
                        trackAdEvent('view', adId, postId);
                        
                        // Stop observing once viewed
                        observer.unobserve(ad);
                    }
                });
            }, observerOptions);
            
            adWrappers.forEach(wrapper => {
                adObserver.observe(wrapper);
                
                // 2. Click Tracking
                wrapper.addEventListener('click', (e) => {
                    // Only track if they clicked an actual link inside the ad
                    if (e.target.closest('a')) {
                        const adId = wrapper.dataset.adId;
                        const postId = wrapper.dataset.postId;
                        trackAdEvent('click', adId, postId);
                    }
                });
            });
        }

        // Helper to send the fetch request
        function trackAdEvent(action, adId, postId) {
            fetch(`${cbpSettings.rest_url}cbp/v1/ads/track`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': cbpSettings.nonce
                },
                body: JSON.stringify({
                    action: action,
                    ad_id: adId,
                    post_id: postId
                })
            }).catch(err => console.error('CBP Ad Track Error:', err));
        }
    }
});
