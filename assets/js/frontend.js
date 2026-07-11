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

    /**
     * ================================================================
     * Frontend Blog Submission Form Handler
     * Handles the [cbp_submit_form] shortcode form via AJAX.
     * ================================================================
     */
    const submitForm = document.getElementById('cbp-blog-submit-form');
    if (submitForm) {

        // --- Image Preview Logic ---
        const fileInput    = document.getElementById('cbp-blog-image');
        const imagePreview = document.getElementById('cbp-image-preview');
        const previewImg   = document.getElementById('cbp-preview-img');
        const removeBtn    = document.getElementById('cbp-remove-image');
        const uploadArea   = document.getElementById('cbp-upload-area');

        if (fileInput) {
            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'inline-block';
                        uploadArea.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                fileInput.value = '';
                previewImg.src = '';
                imagePreview.style.display = 'none';
                uploadArea.style.display = 'block';
            });
        }

        // --- AJAX Form Submission ---
        submitForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const notice    = document.getElementById('cbp-submit-notice');
            const submitBtn = document.getElementById('cbp-submit-btn');
            const btnText   = submitBtn.querySelector('.cbp-btn-text');
            const spinner   = submitBtn.querySelector('.cbp-btn-spinner');

            // Hide previous notice
            notice.style.display = 'none';
            notice.className = 'cbp-submit-notice';

            // Validate required fields client-side first
            const title   = document.getElementById('cbp-blog-title').value.trim();
            const content = document.getElementById('cbp-blog-content').value.trim();

            if (!title) {
                showNotice('error', 'Please enter a blog title.');
                document.getElementById('cbp-blog-title').focus();
                return;
            }
            if (!content) {
                showNotice('error', 'Please enter your blog content.');
                document.getElementById('cbp-blog-content').focus();
                return;
            }

            // Set loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Submitting...';
            spinner.style.display = 'inline-block';

            // Build FormData (supports file upload)
            const formData = new FormData(submitForm);
            formData.append('action', 'cbp_submit_blog');

            fetch(cbpFrontendData.ajax_url, {
                method: 'POST',
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotice('success', data.data.message);
                    submitForm.reset();
                    // Reset image preview
                    if (imagePreview) imagePreview.style.display = 'none';
                    if (uploadArea) uploadArea.style.display = 'block';
                    // Scroll to the notice
                    notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    showNotice('error', data.data.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(() => {
                showNotice('error', 'A network error occurred. Please check your connection and try again.');
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                btnText.textContent = 'Submit for Review';
                spinner.style.display = 'none';
            });
        });

        /**
         * Helper: Show a styled notice message
         * @param {string} type - 'success' or 'error'
         * @param {string} message
         */
        function showNotice(type, message) {
            const notice = document.getElementById('cbp-submit-notice');
            notice.className = `cbp-submit-notice cbp-notice-${type}`;
            notice.textContent = message;
            notice.style.display = 'block';
        }
    }

});
