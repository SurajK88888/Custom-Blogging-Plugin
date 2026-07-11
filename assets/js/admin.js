/**
 * Custom Blog Pro - Admin Settings JS
 */
jQuery(document).ready(function($) {
    
    // Tab switching logic
    $('.cbp-tabs-nav a').on('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all
        $('.cbp-tabs-nav a').removeClass('active');
        $('.cbp-tab-pane').removeClass('active');
        
        // Add active class to clicked tab and corresponding pane
        $(this).addClass('active');
        const targetId = $(this).attr('href');
        $(targetId).addClass('active');

        // Optional: Update URL hash for deep linking
        history.replaceState(null, null, targetId);
    });

    // On load, check if hash exists and activate that tab
    if (window.location.hash) {
        const hash = window.location.hash;
        const targetTab = $('.cbp-tabs-nav a[href="' + hash + '"]');
        if (targetTab.length) {
            targetTab.trigger('click');
        }
    }
    
});
