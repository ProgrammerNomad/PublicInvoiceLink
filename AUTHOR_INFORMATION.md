# PublicInvoiceLink - Author and Repository Information

## Repository Details

**GitHub Repository**: https://github.com/ProgrammerNomad/PublicInvoiceLink
**Author**: ProgrammerNomad
**License**: MIT License (Free and Open Source)
**Version**: 1.0.1

## About This Project

PublicInvoiceLink is a free and open source WHMCS addon that allows clients to access their invoices via secure public links without requiring login credentials. This project was created to streamline the invoice payment process and improve customer experience.

## File Structure and Components

### Actual Project Structure

```text
PublicInvoiceLink/
├── modules/
│   └── addons/
│       └── publicInvoiceLink/
│           ├── publicInvoiceLink.php      # Main addon configuration
│           ├── hooks.php                  # Hook functions for email and admin integration
│           ├── admin_ajax.php             # AJAX endpoint for admin area
│           ├── invoicepdf.tpl            # Public invoice template
│           └── models/
│               └── pilink_access.php     # Database model class
├── README.md                              # Main documentation
├── CHANGELOG.md                          # Version history
├── AUTHOR_INFORMATION.md                 # This file
├── CONTRIBUTING.md                       # Contribution guidelines
└── LICENSE                               # MIT License
```

### Core Files Description

#### 1. Main Addon File (`publicInvoiceLink.php`)

- Core addon configuration and functionality
- Contains all main addon functions:
  - `publicInvoiceLink_config()` - Addon configuration
  - `publicInvoiceLink_activate()` - Installation and database setup
  - `publicInvoiceLink_upgrade()` - Version upgrade handler
  - `publicInvoiceLink_clientarea()` - Public access processing
- Version: 1.0.1
- Author: ProgrammerNomad

#### 2. Hook Functions (`hooks.php`)

- Email integration hooks for automatic link generation
- Admin area integration for manual link creation
- Automatic cleanup when invoices are paid/cancelled
- Functions:
  - `create_pilink_access_token()` - Generate secure access tokens
  - `remove_pilink_access_tokens()` - Clean up expired tokens
  - `add_pilink_admin_button()` - Add admin copy button
  - `disable_non_invoice_pages()` - Restrict access when enabled

#### 3. AJAX Handler (`admin_ajax.php`)

- Handles admin area link generation requests
- Secure token validation and generation
- JSON response formatting for admin interface

#### 4. Database Model (`models/pilink_access.php`)

- Eloquent model for database operations
- Namespace: `PublicInvoiceLink\Models`
- Class: `PilinkAccess`
- Table: `pilink_access_tokens`
- Methods for token generation and client relationships

#### 5. Public Template (`invoicepdf.tpl`)

- Invoice display template for public access
- Handles authentication and invoice rendering
- No login requirements for clients

## Key Features

- **Secure Public Links**: Time-limited secure access to invoices
- **Email Integration**: Seamless integration with WHMCS email templates
- **Configurable Expiration**: Customizable link expiration time
- **Automatic Cleanup**: Links removed when invoices are paid/cancelled
- **Click Tracking**: Audit trail for link usage
- **Modern Architecture**: Built with Laravel Eloquent ORM

## Repository Information

All files include proper headers with:

- Author: ProgrammerNomad
- Copyright: 2025 ProgrammerNomad
- License: MIT License
- Repository: [GitHub Repository](https://github.com/ProgrammerNomad/PublicInvoiceLink)
- Issues: [Report Issues](https://github.com/ProgrammerNomad/PublicInvoiceLink/issues)
- Documentation links

## Key Benefits

1. **Open Source**: MIT License allows free use, modification, and distribution
2. **Professional**: Proper author attribution and licensing
3. **Community**: GitHub repository allows for community contributions
4. **Support**: Issue tracking and documentation
5. **Transparency**: All code is publicly available and auditable

## Installation

1. Download from [GitHub Repository](https://github.com/ProgrammerNomad/PublicInvoiceLink)
2. Upload `publicInvoiceLink` folder to `/modules/addons/`
3. Activate in WHMCS Admin → Setup → Addon Modules
4. Configure settings as needed
5. Add `{$pilink_access_url}` to invoice email templates

## Support and Community

- **Issues**: [Report bugs or request features](https://github.com/ProgrammerNomad/PublicInvoiceLink/issues)
- **Discussions**: GitHub Discussions for community support
- **Documentation**: Comprehensive README and inline code documentation
- **Contributing**: Open to community contributions via pull requests

---

**Made with ❤️ by ProgrammerNomad**  
*Free and Open Source WHMCS Addon*
