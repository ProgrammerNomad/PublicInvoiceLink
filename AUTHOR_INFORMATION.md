# PublicInvoiceLink - Author and Repository Information

## Repository Details

**GitHub Repository**: https://github.com/ProgrammerNomad/PublicInvoiceLink
**Author**: ProgrammerNomad
**License**: MIT License (Free and Open Source)
**Version**: 1.0.0

## About This Project

PublicInvoiceLink is a free and open source WHMCS addon that allows clients to access their invoices via secure public links without requiring login credentials. This project was created to streamline the invoice payment process and improve customer experience.

## File Structure and Components

### 1. Main Addon File (`publicInvoiceLink.php`)
- Core addon configuration and functionality
- Contains all main addon functions:
  - `publicInvoiceLink_config()` - Addon configuration
  - `publicInvoiceLink_activate()` - Installation handler
  - `publicInvoiceLink_upgrade()` - Upgrade handler
  - `publicInvoiceLink_clientarea()` - Public access handler
- Author: ProgrammerNomad
- Version: 1.0.0
- License: MIT License

### 2. Model File (`models/pilink_access.php`)
- Eloquent model for public invoice link functionality
- Handles database operations for public invoice links
- Namespace: `PublicInvoiceLink\Models`
- Class: `PilinkAccess`
- Table: `pilink_access_tokens`

### 3. Hooks File (`hooks.php`)
- WHMCS hook functions for email integration
- Automatic link generation and cleanup
- Integrates with WHMCS email system

### 4. Template File (`invoicepdf.tpl`)
- Invoice display template for public access
- Handles invoice rendering without login requirements

### 5. Additional Files
- `publicInvoiceLink/admin_ajax.php` - AJAX functionality for admin area
- `publicInvoiceLink/hooks.php` - Hook functions for email and client area integration

### 6. Documentation Files
- `README.md` - Comprehensive setup and usage guide
- `LICENSE` - MIT License
- `CONTRIBUTING.md` - Contribution guidelines
- `CHANGELOG.md` - Version history and changes

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
- Repository: https://github.com/ProgrammerNomad/PublicInvoiceLink
- Issues: https://github.com/ProgrammerNomad/PublicInvoiceLink/issues
- Documentation links

## Key Benefits

1. **Open Source**: MIT License allows free use, modification, and distribution
2. **Professional**: Proper author attribution and licensing
3. **Community**: GitHub repository allows for community contributions
4. **Support**: Issue tracking and documentation
5. **Transparency**: All code is publicly available and auditable

## Installation

1. Download from GitHub: https://github.com/ProgrammerNomad/PublicInvoiceLink
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
