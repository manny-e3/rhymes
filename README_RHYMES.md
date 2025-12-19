# 📘 Rhymes – Rovingheights Author Platform

A comprehensive platform for authors to submit books, track sales, and manage royalties with Rovingheights.

## 🚀 Features

### User Roles
- **User**: Can register and submit books for review
- **Author**: Promoted after first book acceptance - access to dashboard, wallet, payouts
- **Admin**: Rovingheights team - review books, manage payouts, system oversight

### Core Functionality
- **Book Submission**: Authors submit books with ISBN, title, genre, price, description
- **Review Process**: Admin review with status tracking (pending → accepted → stocked → rejected)
- **Real-time Sales**: Integration with REV ERP for live sales and inventory data
- **Wallet System**: Track earnings, view sales breakdown by book
- **Payout Requests**: Authors request payouts, admin approval workflow
- **Role-based Access**: Spatie Laravel Permission for secure access control

## 🛠 Tech Stack

- **Backend**: Laravel 12 with Blade views
- **Frontend**: Tailwind CSS with responsive design
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **Roles**: Spatie Laravel Permission
- **ERP Integration**: REV API service layer

## 📋 Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL (XAMPP)

### Installation Steps

1. **Clone and Install Dependencies**
```bash
composer install
npm install
```

2. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Configuration**
Update `.env` with your MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rhymes_platform
DB_USERNAME=root
DB_PASSWORD=
```

4. **REV ERP Integration** (Optional)
```env
REV_BASE_URL=https://api.rev-erp.com
REV_API_KEY=your_rev_api_key_here
REV_WEBHOOK_SECRET=your_webhook_secret_here
REV_SYNC_ENABLED=true
```

5. **Run Migrations and Seeders**
```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=DemoDataSeeder
```

6. **Build Assets**
```bash
npm run build
```

7. **Start Development Server**
```bash
php artisan serve
```

## 👥 Demo Accounts

After running the demo seeder:

- **Admin**: admin@rhymes.com / password
- **Author**: author@rhymes.com / password  
- **User**: user@rhymes.com / password

## 🗂 Database Schema

### Core Tables
- `users` - User accounts with roles and payment details
- `books` - Book submissions with status tracking
- `payouts` - Payout requests and approvals
- `wallet_transactions` - Sales, adjustments, and payout records
- `rev_mappings` - Book mapping to REV ERP system
- `rev_sync_logs` - ERP integration audit trail

### Key Relationships
- User → Books (one-to-many)
- User → Payouts (one-to-many)
- User → WalletTransactions (one-to-many)
- Book → RevMapping (one-to-one)
- Book → WalletTransactions (one-to-many)

## 🔄 REV ERP Integration

### API Endpoints Used
- `POST /api/books` - Create/update book in REV
- `GET /api/sales` - Fetch sales data
- `GET /api/inventory` - Fetch inventory levels
- `GET /api/health` - Connection test

### Sync Process
1. **Book Acceptance**: Auto-sync to REV when admin accepts book
2. **Sales Sync**: Scheduled job pulls sales data and updates wallet
3. **Inventory Sync**: Updates book stock status
4. **Error Handling**: All sync operations logged for troubleshooting

## 📱 User Workflows

### Author Journey
1. **Registration** → User role assigned
2. **Submit Book** → Fill form with book details
3. **Admin Review** → Wait for approval (5-7 days)
4. **Promotion** → First book acceptance promotes to Author
5. **Dashboard Access** → View stats, manage books, wallet
6. **Sales Tracking** → Real-time earnings from REV
7. **Payout Request** → Request withdrawals with admin approval

### Admin Workflow
1. **Review Queue** → See all pending book submissions
2. **Book Evaluation** → Accept/reject with notes
3. **REV Integration** → Accepted books sync to inventory
4. **Payout Management** → Approve/deny author payout requests
5. **System Monitoring** → View sync logs and platform stats

## 🎨 UI/UX Features

- **Responsive Design**: Works on desktop, tablet, mobile
- **Role-based Navigation**: Different menus for each user type
- **Status Indicators**: Color-coded book and payout statuses
- **Real-time Stats**: Dashboard cards with key metrics
- **Form Validation**: Client and server-side validation
- **Success/Error Messages**: Clear user feedback

## 🔐 Security Features

- **Role-based Access Control**: Spatie permissions
- **Route Protection**: Middleware guards
- **CSRF Protection**: Laravel built-in
- **Input Validation**: Comprehensive form validation
- **API Authentication**: Bearer token for REV integration

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure production database
- [ ] Set up REV API credentials
- [ ] Configure mail settings for notifications
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Set up scheduled jobs for REV sync
- [ ] Configure web server (Apache/Nginx)

### Scheduled Jobs
Add to crontab for automated sync:
```bash
* * * * * cd /path/to/rhymes-platform && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 Monitoring & Logs

- **REV Sync Logs**: `rev_sync_logs` table tracks all ERP operations
- **Laravel Logs**: Standard Laravel logging in `storage/logs/`
- **Error Tracking**: Failed sync operations logged with details
- **Performance**: Monitor database queries and API response times

## 🔧 Customization

### Adding New Book Fields
1. Create migration for new columns
2. Update Book model `$fillable`
3. Modify book forms and validation
4. Update REV sync payload if needed

### New User Roles
1. Add role in `RolePermissionSeeder`
2. Create permissions as needed
3. Update middleware and policies
4. Add role-specific views/routes

## 📞 Support

For technical issues or questions:
- Check `rev_sync_logs` for ERP integration issues
- Review Laravel logs for application errors
- Verify database connections and migrations
- Test REV API connectivity with health endpoint

---

**Built with ❤️ for Rovingheights Author Community**
