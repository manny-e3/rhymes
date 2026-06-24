// Function to show confirmation dialog using SweetAlert
function confirmAction(message, callback) {
    // Check if SweetAlert is available
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded');
        // Fallback to native confirm if SweetAlert is not available
        if (confirm(message)) {
            callback();
        }
        return;
    }
    
    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        confirmButtonColor: '#e85347',  // Red color for confirm button
        cancelButtonColor: '#6c757d'    // Gray color for cancel button
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

// Wait for the document to be fully loaded and ensure jQuery is available
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is available
    if (typeof $ !== 'undefined') {
        // Use jQuery's document ready
        $(document).ready(function() {
            setupConfirmationButtons();
        });
    } else {
        // Fallback to vanilla JavaScript
        setupConfirmationButtons();
    }
});

// Function to set up confirmation buttons
function setupConfirmationButtons() {
    console.log('Setting up confirmation buttons...');
    
    // Add confirmation dialog to deactivate buttons
    const deactivateButtons = document.querySelectorAll('[data-confirm-deactivate]');
    
    deactivateButtons.forEach(button => {
        console.log('Found deactivate button:', button);
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const message = this.getAttribute('data-confirm-message') || 'Are you sure you want to deactivate this user? This will prevent them from logging in.';
            
            console.log('Deactivate confirmation dialog triggered', { form, message });
            confirmAction(message, function() {
                console.log('Deactivate confirmed, submitting form');
                if (form) {
                    form.submit();
                }
            });
        });
    });
    
    // Add confirmation dialog to activate buttons
    const activateButtons = document.querySelectorAll('[data-confirm-activate]');
    
    activateButtons.forEach(button => {
        console.log('Found activate button:', button);
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const message = this.getAttribute('data-confirm-message') || 'Are you sure you want to activate this user? This will allow them to log in.';
            
            console.log('Activate confirmation dialog triggered', { form, message });
            confirmAction(message, function() {
                console.log('Activate confirmed, submitting form');
                if (form) {
                    form.submit();
                }
            });
        });
    });
    
    // Also handle any other confirmation buttons that might exist
    const otherConfirmButtons = document.querySelectorAll('[data-confirm-reset], [data-confirm-promote], [data-confirm-delete]');
    otherConfirmButtons.forEach(button => {
        console.log('Found other confirmation button:', button);
        const actionType = button.getAttribute('data-confirm-reset') ? 'reset' :
                          button.getAttribute('data-confirm-promote') ? 'promote' :
                          button.getAttribute('data-confirm-delete') ? 'delete' : 'other';
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const message = this.getAttribute('data-confirm-message') || `Are you sure you want to ${actionType} this user?`;
            
            console.log(`${actionType} confirmation dialog triggered`, { form, message });
            confirmAction(message, function() {
                console.log(`${actionType} confirmed, submitting form`);
                if (form) {
                    form.submit();
                }
            });
        });
    });
}