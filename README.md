[English](README.md)
[Lietuvių](README.LT.md)
# Neko – Last-Mile Logistics & Dispatch Management Platform

**Neko** is a production-grade web-based operations and billing platform for courier and bike-delivery companies. It manages order creation, daily dispatch, workforce/bike assignment, dynamic pricing, and automated invoicing in a single integrated system.

---

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Setup & Installation](#setup--installation)
- [Core Business Workflows](#core-business-workflows)
- [API Documentation](#api-documentation)
- [Database Models](#database-models)
- [Configuration & Settings](#configuration--settings)
- [Security & Permissions](#security--permissions)
- [Backup & Restore](#backup--restore)
- [Development](#development)
- [License](#license)

---

## Overview

Neko is built for logistics operations teams who need to:

1. **Capture and track delivery orders** from multiple clients with complex routing requirements.
2. **Dispatch and assign work** to couriers and bikes in real-time with a drag-and-drop board.
3. **Plan workforce capacity** using a monthly/weekly workload calendar.
4. **Calculate pricing dynamically** based on distance, weight, timing, and client-specific rules.
5. **Generate and send invoices** automatically, with immutable snapshots and PDF/email support.
6. **Manage permissions and audit trails** for compliance and accountability.

### Target Users

- **Dispatchers**: Real-time job assignment and task management.
- **Managers**: Workforce planning, capacity management, performance tracking.
- **Finance**: Invoice management, payment tracking, billing reconciliation.
- **Couriers** (via API): Access workload and delivery information.

---

## Key Features

### 1. Job & Order Management
- Create, update, and track delivery jobs with flexible task types (pickup, drop-off, return, custom).
- Support for multiple package types with quantity thresholds and add-ons.
- Job templating to quickly duplicate recurring routes.
- Field locking to prevent accidental changes during execution.
- Notes and activity tracking per job.

### 2. Dispatch Board
- Day-view dashboard showing unassigned jobs and courier columns.
- Drag-and-drop task assignment between couriers.
- Real-time courier availability and workload visualization.
- Status color-coding by job state and task type.
- Copy/batch operations for rapid assignment.

### 3. Workload Planning
- Monthly and weekly calendar views for capacity planning.
- Courier-to-bike assignment per day with capacity percentage.
- Add/edit/remove workload entries from calendar.
- Free courier and free bike discovery for a selected date.

### 4. Pricing Engine
- **Distance-based pricing** with threshold and step-based billing.
- **Weight-based add-ons**.
- **Timing-based premiums** (early morning, late evening, weekend).
- **Sunday and bank holiday surcharges** (pre-configured for UK 2024–2028).
- **Package type pricing** with base and max quantity thresholds.
- **Same-day return handling** with premium rates.
- **Manual price adjustments** per job.
- Persistent price breakdown stored in `job_prices` table for audit.

### 5. Invoicing & Billing
- **Auto-invoice creation**: When a job reaches status id 14 (billable), it is automatically linked to the next Monday's invoice for its client.
- **Weekly invoice windows**: Invoices cover Monday-to-Sunday delivery periods.
- **Invoice item aggregation**: Multiple jobs grouped into a single invoice item with date range description.
- **Snapshot versioning**: Each generated invoice PDF/email captures an immutable snapshot of VAT, totals, and job details.
- **Template-based email**: Customizable invoice email subject and body per client, with automatic PDF attachment.
- **Invoice locking**: After a configurable number of days (default: 1 day), invoices are locked unless user is admin/superadmin.

### 6. Client & Pricing Configuration
- **Multi-address clients** with separate pickup, billing, and invoice delivery addresses.
- **Distance and weight rule management** per client.
- **Package type assignment** (which package types each client can use).
- **Add-on rule assignment** (surcharges applicable to specific clients).
- **Invoice email templates** per client with variable substitution (`:client_name`, `:invoice_number`, `:invoice_date`, `:invoice_total`).

### 7. Role-Based Access Control (RBAC)
- **Role and permission management** using Spatie Laravel Permission.
- **Granular permissions**: `permission-view`, `permission-edit`, `setting-view`, `setting-edit`, `setting-create`.
- **Admin/SuperAdmin tiers** with elevated privileges (IDs 1–2).
- **Privilege escalation guards** in permission updates (users can only assign permissions they hold).

### 8. Settings & Customization
- **Global settings**: VAT rate, invoice lock age (days).
- **User-specific settings**: Job list sort column/order, drop-off search field preferences.
- Hierarchical settings: User overrides fall back to global defaults.
- Flat JSON storage with dot-notation keys for easy retrieval.

### 9. Data Integrity & Backup
- **SQL dump/restore interface** with selective table export.
- **Protection against accidental data loss**: `users` table is restricted from dump/restore.
- **Chunked file export** for large databases (default 1 MB chunks).
- **Migration-ordered table export** to respect foreign key constraints.

### 10. API & Integration
- **Sanctum token authentication** for mobile and third-party apps.
- **RESTful endpoints** for jobs, users, workloads, user day statuses, user statuses.
- **OpenAPI/Swagger documentation** with detailed examples.
- **Bike assignment endpoint** with zero-argument execution pattern.

---

## Tech Stack

### Backend
- **Framework**: Laravel 10.10
- **Language**: PHP 8.3+
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Sanctum (API tokens)
- **Authorization**: Spatie Laravel Permission 5.10

### Frontend
- **Build Tool**: Vite 6.3.5
- **CSS Framework**: Bootstrap 5.2.3
- **DOM Manipulation**: Vanilla JavaScript + Axios 1.1.2
- **Templating**: Blade (Laravel view engine)
- **Icons**: Bootstrap Icons, FontAwesome

### Documentation & Tooling
- **API Docs**: L5 Swagger (OpenAPI 3.0) + Scramble
- **PDF Generation**: Laravel DomPDF 3.1
- **CSV Handling**: League CSV 9.11
- **Google Maps Integration**: AlexPechkarev Google Maps 10.0

---

## Project Structure

```
Neko/
├── app/
│   ├── Console/               # Artisan commands
│   ├── Exceptions/            # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/       # Web controllers (CUD operations)
│   │   ├── Controllers/Api/   # REST API controllers
│   │   ├── Requests/          # Form validation rules
│   │   ├── Middleware/        # Auth, CORS, permission checks
│   │   └── Kernel.php         # HTTP middleware stack
│   ├── Mail/                  # Email templates (InvoiceSendMail)
│   ├── Models/                # Eloquent models (Job, Invoice, User, etc.)
│   ├── Observers/             # Model lifecycle hooks (JobObserver, PickupTaskObserver)
│   ├── Policies/              # Authorization policies
│   ├── Providers/             # Service container registrations
│   ├── Services/              # Business logic
│   │   ├── InvoicePricingService.php
│   │   ├── InvoiceSnapshotService.php
│   │   ├── JobPriceCalculator.php
│   │   ├── JobPriceSnapshotService.php
│   │   ├── BikeAssignmentService.php
│   │   ├── BackupService.php
│   │   └── SettingsService.php
│   ├── Settings/              # User settings definitions
│   └── View/                  # View composers
├── config/                    # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── mail.php
│   ├── permission.php         # Spatie permissions config
│   └── l5-swagger.php         # Swagger/OpenAPI config
├── database/
│   ├── factories/             # Model factories for seeding/testing
│   ├── migrations/            # Schema definitions
│   └── seeders/               # Database seeders
├── public/
│   ├── files/                 # Static assets (logos, icons)
│   └── build/                 # Vite compiled assets
├── resources/
│   ├── css/                   # SCSS stylesheets
│   ├── js/                    # JavaScript entry points & modules
│   │   ├── app.js
│   │   ├── routes.js          # Frontend route mapping
│   │   ├── job/
│   │   ├── task/
│   │   ├── client/
│   │   └── address/
│   ├── views/                 # Blade templates
│   │   ├── job/
│   │   ├── invoice/
│   │   ├── workload/
│   │   ├── day/
│   │   ├── client/
│   │   ├── role/
│   │   ├── setting/
│   │   └── layouts/
│   └── files/                 # User-uploaded files
├── routes/
│   ├── api.php                # REST API routes (Sanctum protected)
│   ├── web.php                # Web routes (auth middleware)
│   ├── channels.php           # Broadcasting channels
│   └── console.php            # Artisan commands
├── storage/
│   ├── app/                   # Application-generated files
│   ├── logs/                  # Log files
│   └── api-docs/              # Generated API docs
├── tests/                     # PHPUnit tests
│   ├── Feature/
│   └── Unit/
├── composer.json              # PHP dependencies
├── package.json               # Node dependencies
├── vite.config.js             # Vite bundler config
├── phpunit.xml                # PHPUnit config
└── README.md                  # This file
```

---

## Setup & Installation

### Requirements

- PHP 8.3 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Composer
- Node.js 16+ and npm/yarn
- Git

### Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url> Neko
   cd Neko
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node dependencies:**
   ```bash
   npm install
   ```

4. **Create environment file:**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Configure database in `.env`:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=neko
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Run migrations:**
   ```bash
   php artisan migrate
   ```

8. **Seed sample data (optional):**
   ```bash
   php artisan db:seed
   ```

9. **Build frontend assets:**
   ```bash
   npm run build
   # For development with auto-reload:
   npm run dev
   ```

10. **Start the application:**
    ```bash
    php artisan serve
    # Navigate to http://localhost:8000
    ```

### Optional: API Token Generation

Generate a Sanctum token for API access:

```bash
php artisan tinker
>>> $user = User::first();
>>> $token = $user->createToken('AppToken')->plainTextToken;
>>> echo $token;
```

Or visit `/get-token` (non-production only) to generate and display a token.

---

## Core Business Workflows

### Workflow 1: Create and Dispatch a Delivery Job

1. **Manager creates a job** via `/jobs/create` with:
   - Client (billing entity)
   - Pickup location and time window
   - One or more drop-off locations with package types and time windows
   - Optional return task
   - Optional add-ons (food handling, etc.)

2. **Job is saved** and status is set (e.g., "unassigned").

3. **System calculates price** based on distance, weight, timing, and client rules.

4. **Dispatcher views dispatch board** (`/day/dashboard/{date}`) and sees the job in the unassigned column.

5. **Dispatcher drags job to a courier's column** and the task is reassigned.

6. **Job status is updated** (e.g., "assigned", "in progress", "completed").

### Workflow 2: Auto-Invoice Job on Completion

1. **Job completes** and status is changed to id 14 (billable).

2. **JobObserver detected status change** and triggers `assignToInvoice()`.

3. **System calculates next Monday** from job date and looks for an existing invoice with that date for the client.

4. **If no invoice exists**, it creates one with:
   - Invoice date = next Monday
   - Due date = next Monday + 30 days
   - Invoice number = auto-generated
   - Status = "draft"

5. **Job is linked** to an invoice item in that invoice.

6. **InvoicePricingService recalculates** the invoice item and invoice totals.

7. **Finance user views invoice** at `/invoices`, previews PDF, sends email to client.

8. **On send, a snapshot** is created capturing VAT rate, totals, and all line items at that moment.

9. **After invoice lock date** (default 1 day), invoice becomes read-only unless user is admin.

### Workflow 3: Manage Courier Capacity

1. **Manager opens workload calendar** at `/workload/calendar?view=monthly`.

2. **Manager selects a day** and clicks "Add worker".

3. **Modal opens** to assign a courier and bike with capacity percentage.

4. **System stores a workload record** linking the courier, bike, and day.

5. **Dispatch board** now shows the courier as available for that day.

6. **Manager can edit** capacity or delete the workload entry.

---

## API Documentation

Most API endpoints require Sanctum token authentication via the `Authorization: Bearer {token}` header.

Authentication endpoint exception:
- `POST /api/login` — Exchange valid credentials for an API token

### Base URL
```
/api
```

### Key Endpoints

#### Auth
- `POST /api/login` — Authenticate user and return API token

#### Jobs
- `GET /api/jobs` — List jobs with pagination and filtering
- `POST /api/jobs` — Create a new job
- `GET /api/jobs/{job}` — Get job details
- `PUT /api/jobs/{job}` — Update a job
- `DELETE /api/jobs/{job}` — Delete a job

#### Workloads
- `GET /api/workloads` — List workloads
- `POST /api/workloads` — Create workload
- `GET /api/workloads/calendar` — Get calendar view (monthly or weekly)
- `PATCH /api/workloads/{workload}/bike` — Assign or swap a bike

#### Users
- `GET /api/users` — List users
- `POST /api/users` — Create user
- `GET /api/users/{user}` — Get user details
- `PUT /api/users/{user}` — Update user
- `DELETE /api/users/{user}` — Delete user
- `GET /api/users/{user}/workloads` — Get user's workloads
- `GET /api/users/{user}/workloads/{date}` — Get workload for a specific date

#### User Day Statuses
- `GET /api/user-day-statuses` — List daily statuses
- `POST /api/user-day-statuses` — Assign status to user for a day
- `GET /api/user-day-statuses/{user_day_status}` — Get status entry
- `PUT /api/user-day-statuses/{user_day_status}` — Update status
- `DELETE /api/user-day-statuses/{user_day_status}` — Delete status

### OpenAPI/Swagger

Full API documentation is available at:
```
/api/documentation  (L5 Swagger)
/api/docs           (Scramble alternative)
```

Generate Swagger docs:
```bash
php artisan l5-swagger:generate
```

---

## Database Models

### Core Models

| Model | Purpose |
|-------|---------|
| `Job` | Delivery order with pickup/drop-off/return tasks |
| `Task` | Individual pickup, drop-off, return, or custom task |
| `Package` | Drop-off package with type, quantity, address, time window |
| `Pickuptask` | Pickup task details (address, time, client name) |
| `ReturnTask` | Return task for goods retrieval |
| `CustomTask` | Ad-hoc task (not linked to client) |
| `InvoiceItem` | Line item grouping multiple jobs for a client |
| `Invoice` | Weekly invoice with auto-generated number and dates |
| `InvoiceSnapshot` | Immutable version capture of invoice (VAT, totals, jobs) |
| `JobPrice` | Persistent price breakdown row (distance, weight, timing, etc.) |
| `JobTemplate` | Reusable delivery route template |
| `Client` | Delivery service customer with pricing rules |
| `User` | System user (dispatcher, manager, finance, courier) |
| `Workload` | Courier-to-bike assignment for a specific day |
| `Day` | Calendar date record for workload/job grouping |
| `Bike` | Vehicle asset (status: available/occupied) |
| `PackageType` | Delivery package classification (envelope, small box, large box, pallet, etc.) |
| `AddOnRule` | Surcharge rule (food handling, oversize, etc.) |
| `Status` | Job/task status (unassigned, assigned, in progress, completed, etc.) |

### Relationships & Constraints

- A `Job` has many `Task`s.
- A `Task` has one `Pickuptask`, `Package`, `ReturnTask`, or `CustomTask`.
- A `Job` belongs to a `Client` (via `clientToBill_id`).
- An `InvoiceItem` has many `Job`s.
- An `Invoice` has many `InvoiceItem`s.
- A `Workload` links a `User` and `Bike` to a `Day`.
- `AddOn` is polymorphic and attaches to `Job` or `Package`.

---

## Configuration & Settings

### Environment Variables

```env
APP_NAME="Neko"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://neko.example.com

DB_CONNECTION=mysql
DB_HOST=db.example.com
DB_PORT=3306
DB_DATABASE=neko
DB_USERNAME=neko_user
DB_PASSWORD=secure_password

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@neko.example.com

SANCTUM_STATEFUL_DOMAINS=api.neko.example.com
```

### Global Settings (Database)

Global settings are stored in the `settings` table and accessed via `SettingsService`:

- `global.vatRate` — VAT percentage (default: 0.2 or 20%)
- `global.invoiceLockDays` — Days after invoice send to lock (default: 1)

### User Settings (Database)

User-specific settings are stored in the `user_settings` table:

- `models.job.view.index.sortColumn` — Job list sort field (id, clientName, date)
- `models.job.view.index.sortOrder` — Ascending or descending
- `models.job.view.index.dropOffSearchFields` — JSON array of searchable drop-off fields

---

## Security & Permissions

### Authentication

- **Web**: Laravel session-based auth with `/login` and `/register` routes.
- **API**: Sanctum token-based auth with Bearer tokens.

### Authorization

Permissions are managed via Spatie Laravel Permission. Key permissions:

| Permission | Scope |
|-----------|-------|
| `permission-view` | View permissions matrix |
| `permission-edit` | Edit role permissions |
| `setting-view` | View global settings |
| `setting-edit` | Edit global settings |
| `setting-create` | Create SQL backups |

### Privilege Escalation Guard

In `RoleController::updatePermissions()`, users cannot assign permissions they don't already hold. This prevents horizontal or vertical privilege escalation.

### Invoice Locking

Invoices lock after `global.invoiceLockDays` have passed since `sent_at`, unless the user is admin/superadmin:

```php
public function isLockedForUser(?User $user): bool
{
    if (!$this->isCompletedAndPastInvoiceLockDate()) return false;
    if ($user && $user->isAdminOrSuperAdmin()) return false;
    return true;
}
```

---

## Backup & Restore

### Create SQL Dump

1. Visit `/setting/sql-dump` (requires `can:setting-view`).
2. Select tables to include/exclude (users table is restricted).
3. Click "Create Dump" to generate chunked SQL files.
4. Files are stored in `storage/app/backups/sql/`.

### Restore from SQL Dump

1. Go to `/setting/sql-dump` (requires `can:setting-edit`).
2. Upload a previously exported SQL file.
3. System validates file and restores tables (skipping restricted users table).
4. Displays count of executed statements.

### Programmatic Backup

```bash
php artisan backup:create
# or use the service directly in code
```

---

## Development

### Running Tests

```bash
php artisan test
# With coverage
php artisan test --coverage
```

### Code Style

Format code with Laravel Pint:

```bash
composer run pint
```

### Database Migrations

Create a new migration:

```bash
php artisan make:migration create_table_name
```

Run pending migrations:

```bash
php artisan migrate
```

Rollback last batch:

```bash
php artisan migrate:rollback
```

### Tinker (REPL)

Interactive PHP shell with app context:

```bash
php artisan tinker
>>> $job = Job::first();
>>> $job->price();
```

### Debugging

Enable debug mode in `.env`:

```
APP_DEBUG=true
```

Check logs in `storage/logs/`.

---

## License

This project is proprietary software. Unauthorized copying, modification, and distribution are prohibited.

For licensing inquiries, contact the development team.

---

## Support

For issues, feature requests, or general support:

1. Check existing documentation in this README.
2. Review API docs at `/api/documentation`.
3. Consult the codebase for examples.
4. Contact the development team for enterprise support.

---

## Changelog

See `CHANGELOG.md` for version history and breaking changes.

---

**Last Updated**: May 2026  
**Version**: 1.0.0  
**Maintainers**: Development Team
