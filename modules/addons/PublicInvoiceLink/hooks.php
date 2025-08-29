<?php
/**
 * WHMCS Public Invoice Link - Hooks
 * 
 * Hook functions for the Public Invoice Link addon.
 * 
 * @package PublicInvoiceLink
 * @version 1.0.0
 * @author ProgrammerNomad
 * @copyright 2025 ProgrammerNomad
 * @license MIT License
 * @link https://github.com/ProgrammerNomad/PublicInvoiceLink
 * @repository https://github.com/ProgrammerNomad/PublicInvoiceLink
 */

use Illuminate\Database\Capsule\Manager as Capsule;

function create_pilink_access_token($vars) {
  require_once(dirname(__FILE__) . "/models/pilink_access.php");
  

  if (Capsule::table('tblemailtemplates')->where('type','invoice')->where('name',$vars['messagename'])->where('message','like','%pilink_access_url%')->count() > 0) {
    $invoice = \WHMCS\Billing\Invoice::find($vars['relid']);
  
    $access_token = new \PublicInvoiceLink\Models\PilinkAccess;
    
    $access_token->invoice_id = $invoice->id;
    $access_token->user_id = $invoice->clientId;
    $access_token->generate_key();
    $access_token->save();

     
    // Generate access URL
    global $CONFIG;
    $systemurl = ($CONFIG['SystemSSLURL']) ? $CONFIG['SystemSSLURL'].'/' : $CONFIG['SystemURL'].'/';
    $access_url = $systemurl ."index.php?m=publicInvoiceLink&k=".$access_token->key;
    $access_url_html = "<a href='".$access_url."'>".$access_url."</a>";
    
    return array("pilink_access_url" => $access_url,"pilink_access_url_html" => $access_url_html);
  }
  
}

function remove_pilink_access_tokens($vars) {
  // Set all invoice access tokens to expired since the invoice is now paid.
  require_once(dirname(__FILE__) . "/models/pilink_access.php");
 
  \PublicInvoiceLink\Models\PilinkAccess::where('invoice_id', $vars['invoiceid'])->update(array("expiration" => date('Y-m-d',time())));
}

function disable_non_invoice_pages($vars) {
  if (isset($_SESSION['used_pilink_access'])) {
    if (Capsule::table('tbladdonmodules')->where('module','publicInvoiceLink')->where('setting','option1')->first()->value == "on" && ($vars['templatefile'] != "invoice-payment" && $vars['filename'] != "viewinvoice")) {
       // user accessed a page that wasn't the invoice or credit card page
       // log them out and redirect back to the page they were trying to access for full login
       unset($_SESSION['uid']);
       unset($_SESSION['upw']);
       unset($_SESSION['login_auth_tk']);
       unset($_SESSION['used_pilink_access']);
	   header( 'Location: login.php');
	   exit();
    }
  }
	
}




add_hook("EmailPreSend", 1, "create_pilink_access_token");
add_hook("InvoicePaid",1,"remove_pilink_access_tokens");
add_hook("InvoiceCancelled",1,"remove_pilink_access_tokens");
add_hook("ClientAreaPage",1,"disable_non_invoice_pages");

// Add Copy Public Link button to invoice admin pages
function add_pilink_admin_button($vars) {
    // Debug logging
    error_log('PublicInvoiceLink: AdminAreaFooterOutput hook triggered');
    error_log('PublicInvoiceLink: REQUEST_URI = ' . $_SERVER['REQUEST_URI']);
    
    // Only add on invoice edit pages
    if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false && 
        strpos($_SERVER['REQUEST_URI'], 'invoices.php') !== false && 
        isset($_GET['action']) && $_GET['action'] === 'edit' && 
        isset($_GET['id'])) {
        
        error_log('PublicInvoiceLink: Conditions met, adding button script');
        
        $invoiceId = (int)$_GET['id'];
        $token = $_SESSION['token'] ?? '';
        
        error_log('PublicInvoiceLink: Invoice ID = ' . $invoiceId);
        error_log('PublicInvoiceLink: Token = ' . $token);
        
        return <<<HTML
<script type="text/javascript">
$(document).ready(function() {
    console.log('PublicInvoiceLink: Admin script loaded');
    console.log('PublicInvoiceLink: Current URL:', window.location.href);
    console.log('PublicInvoiceLink: Invoice ID: {$invoiceId}');
    
    // Check if the View Invoice button exists
    var viewInvoiceBtn = $('#btnViewInvoice');
    console.log('PublicInvoiceLink: Found View Invoice button:', viewInvoiceBtn.length > 0);
    
    if (viewInvoiceBtn.length === 0) {
        console.log('PublicInvoiceLink: Looking for alternative selectors...');
        console.log('PublicInvoiceLink: Buttons with "View":', $('button:contains("View"), a:contains("View")').length);
        console.log('PublicInvoiceLink: All buttons on page:', $('button, a.btn').length);
    }
    
    // Add the Copy Public Link button after the View Invoice button
    var copyLinkButton = '<button id="btnCopyPublicLink" type="button" class="btn btn-info btn-sm" style="margin-left: 5px;">' +
        '<i class="fas fa-link"></i> Copy Public Link</button>';
    
    if (viewInvoiceBtn.length > 0) {
        viewInvoiceBtn.after(copyLinkButton);
        console.log('PublicInvoiceLink: Button added successfully');
    } else {
        // Fallback: try to find any element with "View Invoice" text
        var fallbackTarget = $('a:contains("View Invoice")').first();
        if (fallbackTarget.length > 0) {
            fallbackTarget.after(copyLinkButton);
            console.log('PublicInvoiceLink: Button added using fallback selector');
        } else {
            console.log('PublicInvoiceLink: Could not find suitable location to add button');
            // Try to add to the page anyway for debugging
            $('body').append('<div style="position: fixed; top: 100px; right: 20px; z-index: 9999; background: red; color: white; padding: 10px;">' +
                'PublicInvoiceLink Debug: Button placement failed. Check console.</div>');
        }
    }
    
    // Add CSS to make sure our button is visible
    $('head').append('<style>#btnCopyPublicLink { z-index: 1000 !important; }</style>');
    
    // Wait a bit and check again in case DOM is still loading
    setTimeout(function() {
        if ($('#btnCopyPublicLink').length === 0) {
            console.log('PublicInvoiceLink: Button still not found after delay, trying pull-right-md-larger');
            var pullRight = $('.pull-right-md-larger').first();
            if (pullRight.length > 0) {
                pullRight.prepend('<button id="btnCopyPublicLink" type="button" class="btn btn-info btn-sm" style="margin-right: 5px;">' +
                    '<i class="fas fa-link"></i> Copy Public Link</button>');
                console.log('PublicInvoiceLink: Button added to pull-right-md-larger container');
            }
        }
    }, 1000);
    
    // Handle button click
    $(document).on('click', '#btnCopyPublicLink', function() {
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
}

add_hook("AdminAreaFooterOutput", 1, "add_pilink_admin_button");


?>