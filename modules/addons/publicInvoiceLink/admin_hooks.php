<?php
/**
 * WHMCS Public Invoice Link - Admin Integration Hook
 * 
 * Adds the Copy Public Link button to invoice admin pages.
 * 
 * @package PublicInvoiceLink
 * @version 1.0.0
 * @author ProgrammerNomad
 * @copyright 2025 ProgrammerNomad
 * @license MIT License
 * @link https://github.com/ProgrammerNomad/PublicInvoiceLink
 * @repository https://github.com/ProgrammerNomad/PublicInvoiceLink
 */

// Add JavaScript and button to invoice admin page
add_hook('AdminAreaFooterOutput', 1, function($vars) {
    // Only add on invoice edit pages
    if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false && 
        strpos($_SERVER['REQUEST_URI'], 'invoices.php') !== false && 
        isset($_GET['action']) && $_GET['action'] === 'edit' && 
        isset($_GET['id'])) {
        
        $invoiceId = (int)$_GET['id'];
        $token = $_SESSION['token'] ?? '';
        
        return <<<HTML
<script type="text/javascript">
$(document).ready(function() {
    // Add the Copy Public Link button after the View Invoice button
    var copyLinkButton = '<button id="btnCopyPublicLink" type="button" class="btn btn-info btn-sm" style="margin-left: 5px;">' +
        '<i class="fas fa-link"></i> Copy Public Link</button>';
    
    $('#btnViewInvoice').after(copyLinkButton);
    
    // Handle button click
    $('#btnCopyPublicLink').click(function() {
        var button = $(this);
        var originalText = button.html();
        
        // Show loading state
        button.html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        button.prop('disabled', true);
        
        // Make AJAX request to generate link
        $.ajax({
            url: '/modules/addons/publicInvoiceLink/admin_ajax.php',
            type: 'POST',
            data: {
                action: 'generate_pilink',
                invoice_id: {$invoiceId},
                token: '{$token}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Copy to clipboard
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(response.url).then(function() {
                            button.html('<i class="fas fa-check"></i> Copied!');
                            button.removeClass('btn-info').addClass('btn-success');
                            
                            // Show success message
                            swal('Success!', 'Public invoice link copied to clipboard!\\n\\nLink expires: ' + response.expires, 'success');
                            
                            // Reset button after 3 seconds
                            setTimeout(function() {
                                button.html(originalText);
                                button.removeClass('btn-success').addClass('btn-info');
                                button.prop('disabled', false);
                            }, 3000);
                        }).catch(function() {
                            // Fallback for older browsers
                            showLinkDialog(response.url, response.expires);
                            button.html(originalText);
                            button.prop('disabled', false);
                        });
                    } else {
                        // Fallback for older browsers
                        showLinkDialog(response.url, response.expires);
                        button.html(originalText);
                        button.prop('disabled', false);
                    }
                } else {
                    swal('Error', response.error || 'Failed to generate link', 'error');
                    button.html(originalText);
                    button.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                swal('Error', 'Failed to generate link: ' + error, 'error');
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });
    
    function showLinkDialog(url, expires) {
        swal({
            title: 'Public Invoice Link Generated',
            text: 'Copy this link to share the invoice publicly:\\n\\n' + url + '\\n\\nExpires: ' + expires,
            type: 'success',
            confirmButtonText: 'Close',
            customClass: 'swal-wide',
            html: true
        });
    }
});
</script>
<style>
.swal-wide {
    width: 80% !important;
}
</style>
HTML;
    }
    
    return '';
});
?>
