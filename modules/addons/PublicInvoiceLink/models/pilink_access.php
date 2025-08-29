<?php
/**
 * WHMCS Public Invoice Link - Access Token Model
 * 
 * Model for handling access token functionality for public invoice links.
 * 
 * @package PublicInvoiceLink\Models
 * @version 1.0.0
 * @author ProgrammerNomad
 * @copyright 2025 ProgrammerNomad
 * @license MIT License
 * @link https://github.com/ProgrammerNomad/PublicInvoiceLink
 * @repository https://github.com/ProgrammerNomad/PublicInvoiceLink
 */

namespace PublicInvoiceLink\Models;
use Illuminate\Database\Capsule\Manager as Capsule;

class PilinkAccess extends \Illuminate\Database\Eloquent\Model {	
  
  protected $fillable = ['key', 'user_id','invoice_id','expiration','clicks'];
  public $timestamps = false;
  protected $table = 'pilink_access_tokens';
  protected $primaryKey = 'key';
  public $incrementing = false;
 

  public function client() {
     return $this->belongsTo('\WHMCS\User\Client','user_id');
  }

  function generate_key() {
	$key2 = Capsule::table('tbladdonmodules')->where('module','publicInvoiceLink')->where('setting','key')->first()->value;
	$expiration_days = Capsule::table('tbladdonmodules')->where('module','publicInvoiceLink')->where('setting','expiration_days')->first()->value;
	if (empty($expiration_days)) {
		$expiration_days = 20;
	}
    $key_created = false;   
    while (!$key_created) { 
      $key = uniqid();
      $timestamp = time();
      $key = sha1($key.$timestamp.$key2);
      \PublicInvoiceLink\Models\PilinkAccess::where('key', $key)->count();
      if (\PublicInvoiceLink\Models\PilinkAccess::where('key', $key)->count() == 0) {
	    $key_created = true;
      }
    }
    $this->key = $key;
    $this->expiration = date('Y-m-d',( time() + (86400 * $expiration_days)) );
    
    return true;
  }
  

}
?>