<?php
/**
 * WHMCS Public Invoice Lfunction publicInvoiceLink_activate() {
  try {
    Capsule::schema()->create(
        'pilink_access_tokens',
        function ($table) {
            /** @var \Illuminate\Database\Schema\Blueprint $table */
            $table->string('key')->primary();
            $table->integer('user_id');
            $table->integer('invoice_id');
            $table->integer('clicks');
            $table->date('expiration');
        }
    );
   } 
   catch (\Exception $e) {
     echo "Unable to create pilink_access_tokens table: {$e->getMessage()}";
   }* Allows clients to access their invoices via a public link without requiring login.
 * This link will expire in configurable days or when the invoice is paid or cancelled.
 * 
 * @package PublicInvoiceLink
 * @version 1.0.0
 * @author ProgrammerNomad
 * @copyright 2025 ProgrammerNomad
 * @license MIT License
 * @link https://github.com/ProgrammerNomad/PublicInvoiceLink
 * @repository https://github.com/ProgrammerNomad/PublicInvoiceLink
 * @documentation https://github.com/ProgrammerNomad/PublicInvoiceLink/blob/main/README.md
 * @issues https://github.com/ProgrammerNomad/PublicInvoiceLink/issues
 * @free Free and open source
 */

use Illuminate\Database\Capsule\Manager as Capsule;
	
function publicInvoiceLink_config() {
    $configarray = array(
    "name" => "Public Invoice Link",
    "description" => "Allows your clients to login via a link sent in invoice emails. This link will expire in configurable days or when the invoice is paid or cancelled. Free and open source addon available at: https://github.com/ProgrammerNomad/PublicInvoiceLink",
    "version" => "1.0.0",
    "author" => "ProgrammerNomad",
    "language" => "english",
    "fields" => array(
    "option1" => array ("FriendlyName" => "Only allow access to the invoice and credit card payment screens upon auto login", "Type" => "yesno", "Size" => "10", "Description" => "", "Default" => ""),
    "expiration_days" => array ("FriendlyName" => "Expire the link after how many days.", "Type" => "text", "Size" => "10", "Description" => "", "Default" => "20"),
    ),

    );
    return $configarray;
}

function publicInvoiceLink_activate() {
  try {
    Capsule::schema()->create(
        'publicInvoiceLink_autologin',
        function ($table) {
            /** @var \Illuminate\Database\Schema\Blueprint $table */
            $table->string('key')->primary();
            $table->integer('user_id');
            $table->integer('invoice_id');
            $table->integer('clicks');
            $table->date('expiration');
        }
    );
   } 
   catch (\Exception $e) {
     echo "Unable to create publicInvoiceLink_autologin table: {$e->getMessage()}";
   }
   
	$key = uniqid();
	
  Capsule::table('tbladdonmodules')->insert(array('module' => 'publicInvoiceLink',"setting"=>"key","value" => $key));

  return array('status'=>'success','description'=>'Module Activated.');
}

function publicInvoiceLink_upgrade($vars) {

   $key = Capsule::table('tbladdonmodules')->where('module','publicInvoiceLink')->where('setting','key')->first()->value;
   if (empty($key)) {
	   $key = uniqid();
	   Capsule::table('tbladdonmodules')->insert(array('module' => 'publicInvoiceLink',"setting"=>"key","value" => $key)); 
   }
   
   try {
     Capsule::schema()->table('pilink_access_tokens', function($table) {
       $table->integer('clicks');
     });
   }
   catch (Exception $e) {
	   
   }
}

function publicInvoiceLink_clientarea($vars) {
  require_once(dirname(__FILE__) . "/models/pilink_access.php");
   if(isset($_GET['k'])) {

    require("configuration.php");
 
    // Lookup Key    
    $access_token = \PublicInvoiceLink\Models\PilinkAccess::where('key',$_GET['k'])->first();

    // Verify that key was found and it is not expired
    if ($access_token != false && (strtotime($access_token->expiration) > time()) ) {
    
      // Lookup client email address and generate an AutoAuth Link
 	    $email = $access_token->client->email;
  	  $_SESSION['used_pilink_access'] = true;
	  
	    if (!isset($access_token->clicks)){ 
	      $access_token->clicks = 1;
      }
      else {
	      $access_token->clicks = $access_token->clicks + 1;
      }
      $access_token->save();

      $results = localAPI("CreateSsoToken",["client_id" => $access_token->user_id, "destination" => "sso:custom_redirect", "sso_redirect_path" => "viewinvoice.php?id=" . $access_token->invoice_id]);
   
      header( 'Location: '. $results['redirect_url']);
      exit();
      
    }
  }  
  
  // Login failed, redirect to login screen.
  header( 'Location: dologin.php');
  exit();  
}
