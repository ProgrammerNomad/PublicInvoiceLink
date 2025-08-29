<?php
/**
 * WHMCS Public Invoice Link - Admin AJAX Handler
 * 
 * Simple AJAX handler for generating public invoice links in admin area.
 * 
 * @package PublicInvoiceLink
 * @version 1.0.0
 * @author ProgrammerNomad
 * @copyright 2025 ProgrammerNomad
 * @license MIT License
 * @link https://github.com/ProgrammerNomad/PublicInvoiceLink
 * @repository https://github.com/ProgrammerNomad/PublicInvoiceLink
 */

// Set JSON response header
header('Content-Type: application/json');

// Basic security check
if (!isset($_POST['action']) || !isset($_POST['invoice_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$action = $_POST['action'];
$invoiceId = (int)$_POST['invoice_id'];

if ($action === 'generate_pilink' && $invoiceId > 0) {
    try {
        // Read WHMCS configuration
        $configFile = dirname(__FILE__) . '/../../../configuration.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        } else {
            throw new Exception('WHMCS configuration not found');
        }
        
        // Connect to database
        $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
        if ($mysqli->connect_error) {
            throw new Exception('Database connection failed: ' . $mysqli->connect_error);
        }
        
        // Get invoice details
        $stmt = $mysqli->prepare("SELECT * FROM tblinvoices WHERE id = ?");
        $stmt->bind_param("i", $invoiceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $invoice = $result->fetch_assoc();
        
        if (!$invoice) {
            throw new Exception('Invoice not found');
        }
        
        // Check if access token already exists for this invoice
        $stmt = $mysqli->prepare("SELECT * FROM pilink_access_tokens WHERE invoice_id = ? AND expiration > NOW()");
        $stmt->bind_param("i", $invoiceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingToken = $result->fetch_assoc();
        
        if ($existingToken) {
            // Use existing token
            $accessKey = $existingToken['key'];
            $expiration = $existingToken['expiration'];
        } else {
            // Generate new access key
            $accessKey = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            // Insert new token
            $stmt = $mysqli->prepare("INSERT INTO pilink_access_tokens (key, user_id, invoice_id, clicks, expiration, created_at, updated_at) VALUES (?, ?, ?, 0, ?, NOW(), NOW())");
            $stmt->bind_param("siis", $accessKey, $invoice['userid'], $invoiceId, $expiration);
            $stmt->execute();
        }
        
        // Generate the public URL
        $systemurl = !empty($systemsslurl) ? $systemsslurl . '/' : $systemurl . '/';
        $publicUrl = $systemurl . "index.php?m=publicInvoiceLink&k=" . $accessKey;
        
        $mysqli->close();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'url' => $publicUrl,
            'expires' => $expiration,
            'message' => 'Public invoice link generated successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to generate link: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request parameters']);
}
?>
