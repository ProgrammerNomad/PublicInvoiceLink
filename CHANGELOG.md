# Changelog - PublicInvoiceLink WHMCS Addon

**Repository**: https://github.com/ProgrammerNomad/PublicInvoiceLink  
**Author**: ProgrammerNomad  
**License**: MIT License (Free and Open Source)

All notable changes to the PublicInvoiceLink WHMCS addon will be documented in this file.

## [1.0.1] - 2025-08-29

### 🚀 Admin Integration Update

Enhanced the addon with powerful admin area integration for seamless public link management.

### ✨ New Features

- **Admin Copy Button**: One-click "Copy Public Link" button on invoice edit pages
- **Smart Token Management**: Intelligent token reuse to prevent conflicts between email and admin-generated links
- **Instant Clipboard Copy**: Generated links are automatically copied to clipboard with visual feedback
- **URL Consistency**: Email links and admin-generated links now use identical format and domain
- **Real-time Generation**: Create public links on-demand without sending emails

### 🔧 Technical Improvements

- **AJAX Endpoint**: New `admin_ajax.php` for secure link generation
- **Enhanced Hooks**: Expanded `hooks.php` with admin area integration
- **Token Optimization**: Email hooks now reuse existing valid tokens instead of creating duplicates
- **Configuration Handling**: Improved WHMCS configuration loading in admin context
- **Error Handling**: Better error messages and fallback mechanisms

### 🛠️ User Experience

- **Visual Feedback**: Success/error notifications with SweetAlert integration
- **Loading States**: Clear visual indicators during link generation
- **Fallback Support**: Multiple button placement strategies for different WHMCS themes
- **Debug Support**: Comprehensive console logging for troubleshooting

### 🔒 Security Enhancements

- **CSRF Protection**: Proper token validation in admin requests
- **Token Reuse**: Eliminates security risks from multiple active tokens
- **Consistent Expiration**: Unified expiration handling across all generation methods

### 📋 How to Use New Features

1. **Admin Integration**: Navigate to any invoice edit page in WHMCS admin
2. **Copy Link**: Click the blue "Copy Public Link" button next to "View Invoice"
3. **Instant Access**: Link is automatically generated and copied to clipboard
4. **Share Securely**: Paste the link to share invoice access with clients

### 🔄 Upgrade Notes

- **Existing Tokens**: All existing tokens continue to work normally
- **Email Templates**: No changes needed to existing email templates
- **Configuration**: No additional setup required - works immediately after update

## [1.0.0] - 2025-08-29

### 🎉 Initial Release

The first stable release of PublicInvoiceLink - a free and open source WHMCS addon that allows clients to access their invoices via secure public links.

### ✨ Features

- **Secure Public Links**: Generate secure, time-limited links for invoice access
- **Configurable Expiration**: Set custom expiration time (default: 20 days)
- **Automatic Cleanup**: Links are automatically removed when invoices are paid or cancelled
- **Email Integration**: Seamlessly integrates with WHMCS email templates using `{$pilink_access_url}` merge field
- **Click Tracking**: Track link usage for audit purposes
- **Optional Restrictions**: Limit access to invoice and payment pages only
- **Modern Architecture**: Built with Eloquent ORM and proper namespacing

### 🔧 Technical Specifications

- **PHP Compatibility**: PHP 7.4+ and PHP 8.x
- **WHMCS Compatibility**: WHMCS 8.0+
- **Database**: Uses WHMCS database with custom table `pilink_access_tokens`
- **Security**: Token-based authentication with configurable expiration
- **Framework**: Laravel Eloquent ORM integration

### 📦 Installation

1. Upload the `publicInvoiceLink` folder to `/modules/addons/`
2. Activate in WHMCS Admin → Setup → Addon Modules
3. Configure settings as needed
4. Add `{$pilink_access_url}` to your invoice email templates

### 🛠️ Configuration Options

- **Restriction Mode**: Option to limit access to invoice and payment pages only
- **Expiration Days**: Customize how many days links remain valid (default: 20)

### 📧 Email Template Integration

Add the merge field `{$pilink_access_url}` to any invoice email template to automatically include the public access link.

### 🔒 Security Features

- Time-based expiration of access links
- Automatic cleanup on invoice status changes
- Secure token generation
- Click tracking and audit trail

### 🎯 Use Cases

- Client-friendly invoice access without login requirements
- Streamlined payment process
- Improved customer experience
- Reduced support tickets for login issues

### 📁 File Structure

```text
modules/addons/publicInvoiceLink/
├── publicInvoiceLink.php      # Main addon configuration file
├── hooks.php                  # Hook functions for email and admin integration
├── admin_ajax.php             # AJAX endpoint for admin area
├── invoicepdf.tpl            # Invoice template for public viewing
└── models/
    └── pilink_access.php     # PilinkAccess model class
```

### 🔗 Links

- **Repository**: [https://github.com/ProgrammerNomad/PublicInvoiceLink](https://github.com/ProgrammerNomad/PublicInvoiceLink)
- **Issues**: [https://github.com/ProgrammerNomad/PublicInvoiceLink/issues](https://github.com/ProgrammerNomad/PublicInvoiceLink/issues)
- **Documentation**: [README.md](https://github.com/ProgrammerNomad/PublicInvoiceLink/blob/main/README.md)
- **License**: MIT License - Free for commercial and personal use

---

**Thank you for using PublicInvoiceLink!** 🚀  
*Made with ❤️ by ProgrammerNomad*
CREATE TABLE `publicInvoiceLink_autologin` (
  `key` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  `expiration` date NOT NULL,
  PRIMARY KEY (`key`)
);
```

#### URL Structure

Public invoice links use the format:

```text
https://yourdomain.com/index.php?m=publicInvoiceLink&k=TOKEN_HERE
```

### Testing Checklist

- ✅ Addon activation/deactivation
- ✅ Database table creation
- ✅ Public link generation via email
- ✅ Public link generation via admin button
- ✅ Email template integration
- ✅ Link expiration functionality
- ✅ Invoice payment/cancellation cleanup
- ✅ Access restriction features
- ✅ Click tracking
- ✅ Admin area integration

---
