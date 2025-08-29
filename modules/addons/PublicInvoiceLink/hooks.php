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


?>