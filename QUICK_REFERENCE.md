# 🚀 Rhymes Platform - Quick Reference Guide

## 📋 Table of Contents
1. [Quick Start](#quick-start)
2. [Common Commands](#common-commands)
3. [User Roles](#user-roles)
4. [API Endpoints](#api-endpoints)
5. [Database Quick Reference](#database-quick-reference)
6. [Troubleshooting](#troubleshooting)

---

## ⚡ Quick Start

### Start Development Environment
```bash
# All-in-one (recommended)
composer run dev

# OR manually
php artisan serve              # Server on http://localhost:8000
php artisan queue:listen       # Queue worker
npm run dev                    # Vite dev server
```

### Access the Application
- **URL**: http://localhost:8000
- **Admin**: admin@rhymes.com / password
- **Author**: author@rhymes.com / password
- **User**: user@rhymes.com / password

---

## 🔧 Common Commands

### Database
```bash
php artisan migrate                    # Run migrations
php artisan migrate:fresh --seed       # Fresh database with seeders
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=DemoDataSeeder
```

### ERPREV Integration
```bash
php artisan rev:test-connection        # Test ERPREV API connection
php artisan rev:sync-sales             # Manually sync sales
php artisan rev:sync-inventory         # Manually sync inventory
php artisan rev:register-book {id}     # Register specific book
```

### Cache & Optimization
```bash
php artisan cache:clear                # Clear application cache
php artisan config:clear               # Clear config cache
php artisan route:clear                # Clear route cache
php artisan view:clear                 # Clear view cache
php artisan optimize                   # Optimize application
```

### Testing
```bash
composer test                          # Run PHPUnit tests
php artisan test                       # Run tests (alternative)
```

### Scheduled Tasks
```bash
php artisan schedule:list              # List scheduled tasks
php artisan schedule:run               # Run scheduled tasks manually
```

---

## 👥 User Roles

### User (Default)
- ✅ Submit books
- ✅ View submission status
- ❌ No dashboard access
- ❌ No wallet access

### Author (Promoted)
- ✅ All User permissions
- ✅ Author dashboard
- ✅ Manage books
- ✅ View wallet & sales
- ✅ Request payouts
- ❌ No admin access

### Admin (Rovingheights)
- ✅ Full platform access
- ✅ Review books
- ✅ Manage users
- ✅ Process payouts
- ✅ View analytics
- ✅ ERPREV monitoring

---

## 🌐 API Endpoints

### ERPREV API
```
Base URL: https://{account_url}/api/1.0/

POST   /register-product/json          # Register book
GET    /get-products-list/json         # List products
GET    /get-stock-list/json            # Inventory levels
GET    /get-salesitems/json            # Sales data
GET    /sold-products-summary/json     # Sales summary
GET    /about/json                     # Test connection
```

### Test Routes (Development Only)
```
GET    /test-erprev                    # Basic test
GET    /test-erprev-full               # Full API test
GET    /test-erprev-simple             # Simple product fetch
GET    /debug-erprev-service           # Service test
GET    /debug-erprev-logs              # View logs
```

---

## 🗄️ Database Quick Reference

### Key Tables
```
users                 - User accounts
books                 - Book submissions
wallet_transactions   - Financial transactions
payouts               - Payout requests
rev_mappings          - ERPREV book mappings
rev_sync_logs         - Integration logs
notifications         - User notifications
user_activities       - Activity tracking
```

### Book Status Flow
```
pending → accepted → stocked
   ↓
rejected
```

### Transaction Types
```
sale       - Author earnings (70% of sale)
adjustment - Manual corrections
payout     - Withdrawals
```

---

## 🐛 Troubleshooting

### ERPREV Connection Issues
```bash
# 1. Test connection
php artisan rev:test-connection

# 2. Check logs
tail -f storage/logs/laravel.log

# 3. Verify .env credentials
cat .env | grep ERPREV

# 4. Test via web route
curl http://localhost:8000/test-erprev
```

### Sales Not Syncing
```bash
# 1. Check scheduled tasks
php artisan schedule:list

# 2. Manual sync
php artisan rev:sync-sales

# 3. Check sync logs
mysql -u root rhymes_platform -e "SELECT * FROM rev_sync_logs ORDER BY id DESC LIMIT 10;"

# 4. Verify book mappings
mysql -u root rhymes_platform -e "SELECT * FROM books WHERE rev_book_id IS NULL;"
```

### Queue Not Processing
```bash
# 1. Check queue worker
ps aux | grep queue

# 2. Restart queue worker
php artisan queue:restart
php artisan queue:listen

# 3. Check failed jobs
php artisan queue:failed
```

### Permission Issues
```bash
# Fix storage permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Fix storage permissions (Windows - run as admin)
icacls storage /grant Users:F /T
icacls bootstrap/cache /grant Users:F /T
```

---

## 📊 Quick Stats Queries

### Platform Overview
```sql
-- Total users by role
SELECT r.name, COUNT(u.id) as count 
FROM users u 
JOIN model_has_roles mr ON u.id = mr.model_id 
JOIN roles r ON mr.role_id = r.id 
GROUP BY r.name;

-- Books by status
SELECT status, COUNT(*) as count 
FROM books 
GROUP BY status;

-- Total revenue
SELECT SUM(amount) as total_revenue 
FROM wallet_transactions 
WHERE type = 'sale';

-- Pending payouts
SELECT COUNT(*) as pending_count, SUM(amount_requested) as total_amount 
FROM payouts 
WHERE status = 'pending';
```

### Author Stats
```sql
-- Top earning authors
SELECT u.name, u.email, SUM(wt.amount) as total_earnings
FROM users u
JOIN wallet_transactions wt ON u.id = wt.user_id
WHERE wt.type = 'sale'
GROUP BY u.id
ORDER BY total_earnings DESC
LIMIT 10;

-- Top selling books
SELECT b.title, u.name as author, COUNT(wt.id) as sales_count, SUM(wt.amount) as earnings
FROM books b
JOIN users u ON b.user_id = u.id
LEFT JOIN wallet_transactions wt ON b.id = wt.book_id AND wt.type = 'sale'
GROUP BY b.id
ORDER BY sales_count DESC
LIMIT 10;
```

---

## 🔑 Environment Variables

### Required Variables
```env
# Database
DB_CONNECTION=mysql
DB_DATABASE=rhymes_platform
DB_USERNAME=root
DB_PASSWORD=

# ERPREV
ERPREV_ACCOUNT_URL=your-account.erprev.com
ERPREV_API_KEY=your_api_key
ERPREV_API_SECRET=your_api_secret
ERPREV_SYNC_ENABLED=true

# Application
APP_URL=http://localhost:8000
APP_ENV=local
APP_DEBUG=true
```

---

## 📁 Important File Locations

### Configuration
```
.env                              # Environment variables
config/services.php               # ERPREV configuration
config/app.php                    # Application config
```

### Services
```
app/Services/RevService.php       # ERPREV integration
app/Services/WalletService.php    # Wallet management
app/Services/PayoutService.php    # Payout processing
```

### Controllers
```
app/Http/Controllers/Admin/       # Admin controllers
app/Http/Controllers/Author/      # Author controllers
app/Http/Controllers/User/        # User controllers
```

### Views
```
resources/views/admin/            # Admin panel views
resources/views/author/           # Author dashboard views
resources/views/auth/             # Authentication views
```

### Logs
```
storage/logs/laravel.log          # Application logs
Database: rev_sync_logs           # ERPREV sync logs
```

---

## 🎯 Common Tasks

### Add New Admin User
```bash
php artisan tinker
```
```php
$user = User::create([
    'name' => 'New Admin',
    'email' => 'newadmin@rhymes.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now()
]);
$user->assignRole('admin');
```

### Manually Process Payout
```bash
php artisan tinker
```
```php
$payout = Payout::find(1);
$service = app(\App\Services\Admin\PayoutManagementService::class);
$service->approvePayout($payout, $payout->amount_requested, 'Approved');
```

### Register Book in ERPREV
```bash
# By book ID
php artisan rev:register-book 1

# OR via tinker
php artisan tinker
```
```php
$book = Book::find(1);
$service = app(\App\Services\RevService::class);
$result = $service->registerProduct($book);
```

### View Sync Logs
```bash
php artisan tinker
```
```php
// Recent sync logs
RevSyncLog::latest()->take(10)->get();

// Failed syncs
RevSyncLog::where('status', 'error')->latest()->get();

// Sales syncs
RevSyncLog::where('area', 'sales')->latest()->take(5)->get();
```

---

## 🔄 Deployment Checklist

### Pre-Deployment
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Set up ERPREV production credentials
- [ ] Configure mail settings
- [ ] Set up SSL certificate

### Optimization
```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Scheduled Tasks (Cron)
```bash
# Add to crontab
* * * * * cd /path/to/rhymes-platform && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker (Supervisor)
```ini
[program:rhymes-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/rhymes-platform/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/rhymes-platform/storage/logs/queue.log
```

---

## 📞 Support Contacts

- **ERPREV Support**: info@erprev.com
- **ERPREV API Docs**: https://erprev.com/doc/api/
- **Laravel Docs**: https://laravel.com/docs

---

## 🎓 Useful Laravel Artisan Commands

```bash
php artisan list                  # List all commands
php artisan route:list            # List all routes
php artisan migrate:status        # Migration status
php artisan queue:work            # Start queue worker
php artisan queue:failed          # List failed jobs
php artisan queue:retry all       # Retry failed jobs
php artisan tinker                # Laravel REPL
php artisan make:controller       # Create controller
php artisan make:model            # Create model
php artisan make:migration        # Create migration
php artisan make:seeder           # Create seeder
php artisan make:command          # Create command
```

---

**Last Updated:** November 27, 2025  
**Version:** 1.0.0
