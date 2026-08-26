# Nazmart - Multi-Tenant E-commerce Platform

Nazmart is a comprehensive multi-tenant e-commerce solution built with Laravel, designed to allow multiple independent online shops to operate on a single installation. Each tenant (shop owner) gets their own isolated environment with dedicated database, custom domain support, and modular functionality.

## 🌟 Key Features

- **Multi-Tenancy Architecture**: Complete tenant isolation with separate databases and file storage
- **Subscription Management**: Flexible pricing plans with automated renewals and feature restrictions
- **Modular System**: 30+ modules for extended functionality (Products, Blog, Campaigns, Shipping, etc.)
- **Custom Domains**: Support for both subdomains and custom domain mapping
- **Multiple Themes**: Theme marketplace with tenant-specific theme assignments
- **Payment Gateway Integration**: Multiple payment gateways with tenant-specific configurations
- **Advanced Inventory Management**: Stock tracking, variants, and warehouse management
- **Mobile App Support**: Native mobile app API endpoints
- **Analytics & Reporting**: Comprehensive sales reports and analytics
- **Multi-language Support**: RTL/LTR language support with translation management

## 🏗️ Architecture Overview

Nazmart follows a **database-per-tenant** multi-tenancy pattern using the [Stancl/Tenancy](https://tenancyforlaravel.com/) package:

- **Landlord (Central)**: Manages tenants, subscriptions, pricing plans, and global settings
- **Tenant**: Individual shop instances with isolated databases and functionality

### Core Components

```
nazmart/
├── core/                          # Main Laravel application
│   ├── app/                      # Application logic
│   ├── Modules/                  # Modular functionality (30+ modules)
│   ├── database/                 # Central database migrations
│   └── resources/               # Views and assets
├── assets/                      # Shared assets and tenant files
└── docs/                       # Documentation (generated)
```

## 🚀 Quick Start

### System Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer 2.x
- Node.js 16+ & NPM

### Installation

1. **Clone Repository**
   ```bash
   git clone <repository-url> nazmart
   cd nazmart/core
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nazmart_central
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   TENANT_DATABASE_PREFIX=nazmart_tenant_
   CENTRAL_DOMAIN=localhost
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build Assets**
   ```bash
   npm run production
   ```

## 📊 Multi-Tenant Architecture

### Tenant Lifecycle

1. **User Registration**: Customer creates account on landlord domain
2. **Plan Selection**: User chooses pricing plan and features
3. **Payment Processing**: Secure payment through configured gateways
4. **Tenant Creation**: Automated tenant provisioning process
5. **Database Setup**: Isolated database creation and migration
6. **Domain Assignment**: Subdomain or custom domain mapping
7. **Initial Data Seeding**: Theme and essential data setup

### Database Structure

- **Central Database**: Stores tenants, users, pricing plans, payments, and global settings
- **Tenant Databases**: Individual databases per tenant with complete isolation
- **Shared Resources**: Assets, themes, and modules are shared but tenant-scoped

## 🛍️ Shop Creation Journey

The shop creation process is fully automated:

1. **Frontend Registration** → User selects plan and provides shop details
2. **Payment Processing** → Secure payment through multiple gateways
3. **Tenant Provisioning** → Database creation and domain setup
4. **Theme Installation** → Selected theme deployment
5. **Admin Credentials** → Automatic admin account creation
6. **Email Notifications** → Credentials and setup instructions sent

For detailed technical implementation, see [Shop Creation Documentation](docs/shop-creation.md).

## 📦 Module System

Nazmart features a comprehensive module system with 30+ modules:

- **Core Modules**: Attributes, Products, Categories, Orders
- **Marketing**: Campaigns, Coupons, Newsletters
- **Content**: Blog, Pages, SEO Tools
- **Shipping**: Multiple shipping methods and zones
- **Payment**: Multiple payment gateway integrations
- **Analytics**: Sales reports, customer analytics
- **Mobile**: API endpoints for mobile applications

See [Module Documentation](docs/modules/) for detailed information on each module.

## 💳 Subscription Management

### Pricing Plans
- **Feature-based restrictions**: Control access to modules and features
- **Storage limitations**: File storage quotas per plan
- **Theme access**: Plan-specific theme availability
- **Payment gateway access**: Selective payment method availability

### Renewal Process
- **Automatic Renewals**: Subscription auto-renewal with grace periods
- **Payment Reminders**: Email notifications before expiration
- **Grace Periods**: Configurable grace periods for expired subscriptions
- **Downgrade/Upgrade**: Seamless plan changes with prorated billing

For detailed subscription management, see [Subscription Documentation](docs/subscription-management.md).

## 🌐 Domain Management

### Subdomain Support
- Automatic subdomain creation: `{tenant}.{central-domain}`
- DNS configuration automation
- SSL certificate management

### Custom Domains
- Full custom domain mapping support
- Domain verification process
- Automatic SSL certificate provisioning
- DNS management integration

## 🔧 Configuration

### Environment Variables
```env
# Multi-tenancy Configuration
CENTRAL_DOMAIN=yourdomain.com
TENANT_DATABASE_PREFIX=nazmart_tenant_

# Storage Configuration
FILESYSTEM_DISK=local
AWS_BUCKET=your-bucket

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com

# Payment Gateway Configuration
PAYPAL_MODE=sandbox
STRIPE_KEY=your_stripe_key
```

## 🔒 Security Features

- **Tenant Isolation**: Complete database and file separation
- **CSRF Protection**: Built-in Laravel CSRF protection
- **SQL Injection Prevention**: Eloquent ORM protection
- **File Upload Security**: Secure file handling and validation
- **User Authentication**: Multi-guard authentication system
- **Permission Management**: Role-based access control

## 📈 Performance Optimization

- **Database Optimization**: Proper indexing and query optimization
- **Caching System**: Redis/Memcached support with tenant isolation
- **Asset Optimization**: Minification and compression
- **Image Optimization**: Automatic image compression and resizing
- **CDN Support**: Amazon S3 and CloudFront integration
- **Queue System**: Background job processing

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage
```

## 📚 Documentation

- [Architecture Overview](docs/architecture.md)
- [Multi-Tenant Setup](docs/multi-tenancy.md)
- [Shop Creation Process](docs/shop-creation.md)
- [Module Development](docs/modules/)
- [Subscription Management](docs/subscription-management.md)
- [Tenant Renewal Process](docs/tenant-renewal.md)
- [API Documentation](docs/api/)
- [Deployment Guide](docs/deployment.md)

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: [docs/](docs/)
- **Issues**: [GitHub Issues](../../issues)
- **Community**: [Community Forum](#)

---

**Nazmart** - Empowering entrepreneurs to build successful online businesses with a robust, scalable multi-tenant e-commerce platform.