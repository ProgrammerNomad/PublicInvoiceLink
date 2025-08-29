# Changelog - PublicInvoiceLink WHMCS Addon

**Repository**: https://github.com/ProgrammerNomad/PublicInvoiceLink  
**Author**: ProgrammerNomad  
**License**: MIT License (Free and Open Source)

All notable changes to the PublicInvoiceLink WHMCS addon will be documented in this file.

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

```
modules/addons/publicInvoiceLink/
├── publicInvoiceLink.php          # Main addon file
├── hooks.php                      # Hook functions
├── invoicepdf.tpl                # Invoice template
└── models/
    └── pilink_access.php         # PilinkAccess model

public_invoice_link/
├── ajax.php                      # AJAX handlers
└── hooks.php                     # Additional hooks
```

### 🔗 Links

- **Repository**: [https://github.com/ProgrammerNomad/PublicInvoiceLink](https://github.com/ProgrammerNomad/PublicInvoiceLink)
- **Issues**: [https://github.com/ProgrammerNomad/PublicInvoiceLink/issues](https://github.com/ProgrammerNomad/PublicInvoiceLink/issues)
- **Documentation**: [README.md](https://github.com/ProgrammerNomad/PublicInvoiceLink/blob/main/README.md)
- **License**: MIT License - Free for commercial and personal use

---

**Thank you for using PublicInvoiceLink!** 🚀  
*Made with ❤️ by ProgrammerNomad*

##### `hooks.php`
- ✅ Updated model include path from `invoice_login.php` to `auto_login.php`
- ✅ Updated class references from `\ServerPing\InvoiceLogin\InvoiceLogin` to `\PublicInvoiceLink\Models\AutoLogin`
- ✅ Updated module parameter in autologin URL from `m=invoicelogin` to `m=publicInvoiceLink`
- ✅ Updated module references in database queries from `invoicelogin` to `publicInvoiceLink`

##### `invoicepdf.tpl`
- ✅ Updated include path from `invoicelogin/models/invoice_login.php` to `publicInvoiceLink/models/auto_login.php`

### Database Migration Notes

**Important**: When upgrading from the old version, you may need to:

1. **Rename the database table**:
   ```sql
   RENAME TABLE `serverping_invoicelogin` TO `publicInvoiceLink_autologin`;
   ```

2. **Update module references in tbladdonmodules**:
   ```sql
   UPDATE `tbladdonmodules` SET `module` = 'publicInvoiceLink' WHERE `module` = 'invoicelogin';
   ```

3. **Update email templates**:
   - No changes needed - the merge fields `{$auto_login_link}` and `{$auto_login_link_html}` remain the same

### New Features in v2.0
- ✅ Improved error handling in database table creation
- ✅ Better code organization and naming conventions
- ✅ Enhanced documentation
- ✅ Consistent naming throughout the codebase

### Migration Guide

#### For New Installations
1. Upload the `publicInvoiceLink` folder to `/modules/addons/`
2. Activate the addon in WHMCS admin area
3. Configure settings as needed

#### For Existing Users (Upgrading from InvoiceLogin)
1. **Backup your database** before proceeding
2. Upload the new `publicInvoiceLink` folder to `/modules/addons/`
3. Run the database migration queries above
4. Remove the old `invoicelogin` folder from `/modules/addons/`
5. Reactivate the addon in WHMCS admin area

### Technical Details

#### Module Configuration
```php
"name" => "Public Invoice Link"
"description" => "Allows your clients to login via a link sent in invoice emails. This link will expire in 20 days or when the invoice is paid or cancelled."
"version" => "2.0"
"author" => "PublicInvoiceLink"
```

#### Database Schema
```sql
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
New auto-login URLs use the format:
```
https://yourdomain.com/index.php?m=publicInvoiceLink&k=TOKEN_HERE
```

### Backward Compatibility
- ⚠️ **Not backward compatible** due to module name changes
- Old auto-login links will not work after upgrade
- Database migration required for existing installations
- Email templates using merge fields will continue to work

### Testing Checklist
- ✅ Addon activation/deactivation
- ✅ Database table creation
- ✅ Auto-login link generation
- ✅ Email template integration
- ✅ Link expiration functionality
- ✅ Invoice payment/cancellation cleanup
- ✅ Access restriction features
- ✅ Click tracking

---

## Previous Versions

### Version 1.13 (Original ServerPing Release)
- Basic auto-login functionality
- Email integration
- Configurable expiration
- Page access restrictions
