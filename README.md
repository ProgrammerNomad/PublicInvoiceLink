# PublicInvoiceLink - WHMCS Addon

**Free and Open Source WHMCS Addon**

[![GitHub Repository](https://img.shields.io/badge/GitHub-Repository-blue)](https://github.com/ProgrammerNomad/PublicInvoiceLink)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Version](https://img.shields.io/badge/Version-1.0.1-green)](https://github.com/ProgrammerNomad/PublicInvoiceLink/releases)

## Author Information

**ProgrammerNomad**
- GitHub: [@ProgrammerNomad](https://github.com/ProgrammerNomad)
- Repository: [https://github.com/ProgrammerNomad/PublicInvoiceLink](https://github.com/ProgrammerNomad/PublicInvoiceLink)
- Issues: [Report Issues](https://github.com/ProgrammerNomad/PublicInvoiceLink/issues)
- License: MIT License (Free and Open Source)

## Overview

PublicInvoiceLink is a WHMCS addon that allows clients to access their invoices via secure public links without requiring login credentials. This link expires after a configurable number of days or when the invoice is paid or cancelled.

## Features

- **Secure Public Links**: Generate secure, time-limited links for invoice access
- **Admin Integration**: One-click "Copy Public Link" button on invoice edit pages ✨ **NEW in v1.0.1**
- **Email Integration**: Seamlessly integrates with WHMCS email templates using `{$pilink_access_url}` merge field
- **Smart Token Management**: Intelligent token reuse prevents conflicts between email and admin-generated links ✨ **NEW in v1.0.1**
- **Instant Clipboard Copy**: Generated links are automatically copied to clipboard ✨ **NEW in v1.0.1**
- **Configurable Expiration**: Set custom expiration time (default: 20 days)
- **Automatic Cleanup**: Links are automatically removed when invoices are paid or cancelled
- **Click Tracking**: Track link usage for audit purposes
- **Optional Restrictions**: Limit access to invoice and payment pages only

## Installation

1. Upload the `publicInvoiceLink` folder to your WHMCS `/modules/addons/` directory
2. Login to your WHMCS admin area
3. Navigate to Setup → Addon Modules
4. Find "Public Invoice Link" and click "Activate"
5. Configure the settings as needed

## File Structure

```text
modules/addons/publicInvoiceLink/
├── publicInvoiceLink.php      # Main addon configuration file
├── hooks.php                  # Hook functions for email and admin integration
├── admin_ajax.php             # AJAX endpoint for admin area
├── invoicepdf.tpl            # Invoice template for public viewing
└── models/
    └── pilink_access.php     # PilinkAccess model class
```

## Database Schema

The addon creates a table `pilink_access_tokens` with the following structure:

```sql
CREATE TABLE `pilink_access_tokens` (
  `key` varchar(255) NOT NULL PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `expiration` date NOT NULL
);
```

## Configuration

### Available Settings

- **Restriction Mode**: Limit access to invoice and payment pages only
- **Expiration Days**: Number of days links remain valid (default: 20)

## Usage

### Email Integration (Automatic)

Add the merge field `{$pilink_access_url}` to any invoice email template to automatically include the public access link.

Available merge fields:

- `{$pilink_access_url}` - Plain text link
- `{$pilink_access_url_html}` - HTML formatted link

Example email template:

```html
<p>Click here to view your invoice: {$pilink_access_url_html}</p>
```

### Admin Area Integration ✨ **NEW in v1.0.1**

Generate public invoice links directly from the WHMCS admin area:

1. **Navigate** to any invoice edit page (`Billing → Invoices → Edit Invoice`)
2. **Look for** the blue "Copy Public Link" button next to the "View Invoice" button
3. **Click** the button to instantly generate and copy the public link
4. **Share** the copied link with your client via any communication method

**Features:**

- ✅ One-click link generation and clipboard copy
- ✅ Visual feedback with success notifications
- ✅ Smart token reuse (same link as email if already generated)
- ✅ Instant access without sending emails

## How It Works

1. When an invoice email is sent, the system checks if the email template contains `%pilink_access_url%`
2. If found, a secure access token is generated and stored in the database
3. The token is included in the email as a clickable link
4. When clicked, the client is automatically logged in and redirected to the invoice
5. The token expires after the configured time or when the invoice is paid/cancelled

## Technical Details

### Main Functions

#### `publicInvoiceLink_config()`
Returns the addon configuration array with settings and metadata.

#### `publicInvoiceLink_activate()`
Creates the database table and initializes the addon.

#### `publicInvoiceLink_upgrade($vars)`
Handles database schema updates during upgrades.

#### `publicInvoiceLink_clientarea($vars)`
Processes public access requests and handles client authentication.

### Hook Functions

#### `create_pilink_access_token($vars)`
- **Hook**: EmailPreSend
- **Purpose**: Generates access tokens for invoice emails
- **Returns**: Array with `pilink_access_url` and `pilink_access_url_html` merge fields

#### `remove_pilink_access_tokens($vars)`
- **Hooks**: InvoicePaid, InvoiceCancelled
- **Purpose**: Expires access tokens when invoices are paid or cancelled

#### `disable_non_invoice_pages($vars)`
- **Hook**: ClientAreaPage
- **Purpose**: Restricts access to only invoice/payment pages when restriction mode is enabled

### Model Class: PilinkAccess

Located in `models/pilink_access.php`

#### Properties
- `$table = 'pilink_access_tokens'`
- `$primaryKey = 'key'`
- `$timestamps = false`
- `$fillable = ['key', 'user_id', 'invoice_id', 'expiration', 'clicks']`

#### Methods
- `generate_key()` - Generates unique secure access tokens
- `client()` - Relationship to WHMCS client

## Security Features

- **Unique Tokens**: Each access link uses a unique, secure hash
- **Time Expiration**: Links automatically expire after configured time
- **Status-based Expiration**: Links expire when invoices are paid or cancelled
- **Click Tracking**: Monitor access attempts for security auditing
- **Restricted Access**: Optional limitation to invoice and payment pages only

## Support

- **Repository**: [https://github.com/ProgrammerNomad/PublicInvoiceLink](https://github.com/ProgrammerNomad/PublicInvoiceLink)
- **Issues**: [Report bugs or request features](https://github.com/ProgrammerNomad/PublicInvoiceLink/issues)
- **License**: MIT License - Free for commercial and personal use
- **Documentation**: This README file provides comprehensive setup and usage information

## Requirements

- **WHMCS**: Version 8.0 or higher
- **PHP**: Version 7.4 or higher
- **Database**: MySQL/MariaDB (part of WHMCS installation)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Made with ❤️ by ProgrammerNomad**  
*Free and Open Source WHMCS Addon*
