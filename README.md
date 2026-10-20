# Grading System

A role-based school grading and records application for managing students, enrollments, subjects, grades, homerooms, attendance, conduct, and academic reports from one interface.

## Project goal

The system is designed to replace fragmented or legacy grading workflows with a centralized application for school administrators, office staff, homeroom advisers, and subject teachers. It provides separate operational and administrative panels, keeps academic records organized by school year and grade level, and produces printable student records such as report cards and transcripts.

## Main capabilities

- Role-aware access for administrators, principals, office staff, homeroom advisers, and teachers
- Student profiles, enrollment history, grade levels, sections, and school years
- Subject setup and subject-teacher assignments
- Grade entry and optional spreadsheet-based grade imports
- Homeroom management, attendance, comments, and deportment records
- Graduate and award records
- PDF report cards and transcripts of records
- Migration commands for importing data from a legacy database
- Configurable school-specific features and report templates
- Administrative health checks, backups, logs, and file storage

## Tech stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.3+, Laravel 13, Eloquent ORM |
| Admin and application UI | Filament 5, Livewire-based server-rendered panels, Blade |
| Frontend styling | Tailwind CSS 4, Filament UI, Instrument Sans |
| Asset pipeline | Vite 8, Laravel Vite Plugin |
| Database | Eloquent ORM with MySQL configured by `.env.example`; SQLite is also supported and used by the legacy-import workflow |
| Legacy data import | A second configurable database connection for the old system |
| PDF/report generation | Blade templates and `wkhtmltopdf` through Laravel Snappy; DOMPDF and Browsershot are also installed |
| Browser automation | Puppeteer |
| Monitoring and operations | Laravel Telescope, Laravel Debugbar, Spatie Laravel Health, Spatie Laravel Backup |
| Testing and quality | PHPUnit 12, Laravel Pint, Mockery, Faker |

## Application structure

The application exposes two Filament panels:

- `/app` — the main workspace for office staff, teachers, and homeroom advisers
- `/admin` — administration for users, school years, sections, subjects, settings, backups, and health checks; access is restricted to administrators

The root URL redirects to `/app`. Report endpoints are available under `/report`, while the report layouts live in `resources/views/reports`. School-specific helpers and templates currently exist for BIS and HCGS configurations.

## Requirements

- PHP 8.3 or newer with the extensions required by Laravel and the selected database
- Composer
- Node.js and npm
- MySQL for the provided environment template, or SQLite/another Laravel-supported database
- `wkhtmltopdf` for PDF output outside the local environment

If legacy data will be imported, access to the source database and its appropriate PHP database driver are also required.

## Local setup

1. Clone the repository and enter the project directory.
2. Create the environment file and configure the database, school details, and feature flags:

   ```bash
   cp .env.example .env
   ```

3. Install and initialize the application:

   ```bash
   composer run setup
   ```

   This installs PHP and JavaScript dependencies, preserves the configured `.env`, generates the application key, runs database migrations, and builds the frontend assets.

4. Optionally seed a development user:

   ```bash
   php artisan db:seed
   ```

5. Start the development services:

   ```bash
   composer run dev
   ```

The development command starts the Laravel server, queue listener, application log viewer, and Vite development server. Open `http://localhost:8000`; the application redirects to the main panel.

### Manual setup

If you prefer to run each step separately:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

The supplied `.env.example` is configured for MySQL, so create the named database and provide valid `DB_*` credentials before migrating. To use SQLite instead, create `database/database.sqlite`, set `DB_CONNECTION=sqlite`, and remove or leave unused the other `DB_*` values.

## Configuration

Besides Laravel's standard environment variables, the application uses these project-specific settings.

### School identity

```dotenv
APP_NAME="School Name"
SCHOOL_NAME="School Name"
SCHOOL_ADDRESS="School Address"
SCHOOL_COUNTRY="Country"
SCHOOL_QUARTER_TERM=4
```

`APP_NAME` also selects school-specific report views in parts of the application, so it must match an available report template directory when PDF reports are enabled.

### Optional features

The following flags default to `false` and can be enabled per deployment:

```dotenv
FEATURE_SECTION=false
FEATURE_GRADE_IMPORT=false
FEATURE_CARD_TEMPLATE=false
FEATURE_HOMEROOM_DEPORTMENT=false
FEATURE_STUDENT_LRN=false
FEATURE_MIDQUARTER=false
FEATURE_HOUSE_TEAM=false
```

After changing environment values in a cached environment, run `php artisan config:clear`.

### Legacy database migration

Set the `OLD_DB_*` variables to connect to the legacy data source:

```dotenv
OLD_DB_CONNECTION=mysql
OLD_DB_HOST=127.0.0.1
OLD_DB_PORT=3306
OLD_DB_DATABASE=legacy_grading_system
OLD_DB_USERNAME=root
OLD_DB_PASSWORD=
```

Create the new application schema first, then run the migration pipeline:

```bash
php artisan migrate
php artisan migrate:all
```

Use `php artisan migrate:all --fresh` only when you intentionally want the command to reset target data. Individual import commands such as `migrate:students`, `migrate:subjects`, and `migrate:grades` are also available.

## Reports and PDF generation

Quarter reports and transcripts are rendered from Blade templates. In a local environment, report routes return HTML for easier development and debugging. In non-local environments, the same views are converted to A4 portrait PDFs using `wkhtmltopdf` through Laravel Snappy.

Before deploying PDF output, configure the Snappy binary paths for the target operating system and verify that the selected school report templates exist.

## Development commands

```bash
# Run the automated test suite
composer test

# Format PHP code
./vendor/bin/pint

# Build production frontend assets
npm run build

# Run application health checks
php artisan health:check

# Create an application backup
php artisan backup:run
```

## Key directories

```text
app/Filament/               Filament panels, pages, resources, imports, and exports
app/Helpers/                Grade formulas and school-specific behavior
app/Http/Controllers/       Report and legacy-migration controllers
app/Models/                 Academic and system data models
database/migrations/        Application database schema
resources/views/reports/    Report-card and transcript templates
config/system-features.php  Deployment feature flags
tests/                      PHPUnit feature and unit tests
```

## Current implementation notes

- Quarter report and transcript generation are implemented.
- Award, mid-quarter, and subject reports are not currently exposed; only implemented report routes are registered.
- The bundled seeder creates only a development test user; production users and roles should be provisioned through an approved deployment or migration process.

## License

This project currently inherits the MIT license declaration from its Laravel project metadata. Confirm the intended project license before public distribution.
