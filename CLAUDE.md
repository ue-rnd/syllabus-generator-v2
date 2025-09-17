# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel + Livewire + Filament** application for managing academic institutions (colleges, departments, programs, courses) with a syllabus generation focus.

## Development Commands

### Primary Development Commands
```bash
# Start full development environment (server + queue + vite)
composer dev

# Run tests with config clearing
composer test

# Build frontend assets
npm run build

# Start frontend development server only
npm run dev

# Laravel commands
php artisan serve
php artisan test
php artisan queue:listen --tries=1
```

### Code Quality
```bash
# Laravel Pint (code formatting)
./vendor/bin/pint

# Run specific tests
php artisan test --filter=TestName
```

## Technology Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Livewire with Flux UI components, Tailwind CSS 4.0
- **Admin Panel**: Filament 4.0 with custom resource organization
- **Build**: Vite with custom Tailwind integration
- **Testing**: Pest PHP testing framework
- **Database**: SQLite (development), soft deletes enabled
- **PDF Generation**: Spatie Laravel PDF with Browsershot/Puppeteer

## Architecture Patterns

### Filament Resource Architecture

**CRITICAL**: Resources follow a **modular schema pattern** - never put forms/tables directly in Resource classes:

```php
// ✅ Correct approach
public static function form(Schema $schema): Schema {
    return CollegeForm::configure($schema);  // Delegates to separate schema class
}
```

**Required structure for each resource:**
- `ResourceName/` directory containing:
  - `ResourceNameResource.php` (main resource)
  - `Schemas/ResourceNameForm.php` (form schema)
  - `Schemas/ResourceNameInfolist.php` (view schema)
  - `Tables/ResourceNamesTable.php` (table configuration)
  - `Pages/` (custom pages)

### Model Conventions

All domain models follow consistent patterns:
- **Soft deletes**: `use SoftDeletes;`
- **Standard scopes**: `scopeActive()`, `scopeOrdered()`
- **JSON casting**: Arrays stored as JSON (e.g., `'objectives' => 'array'`)
- **Accessor patterns**: `getLogoUrlAttribute()` for computed properties
- **Status methods**: `isActive()` for boolean checks

### Data Hierarchy

**College → Department → Program → Course** relationship structure:
- Colleges have many Departments and Courses directly
- Departments belong to Colleges, have many Programs
- Programs belong to Departments
- Courses belong to Colleges (not Departments)

### Livewire Components

- **Auth components**: Pre-built in `app/Livewire/Auth/` with proper validation attributes
- **Layout**: Uses `#[Layout('components.layouts.auth')]` attribute
- **Validation**: Uses `#[Validate()]` attributes, not validate() rules
- **Navigation**: Uses `redirectIntended()` with `navigate: true`

### Database Patterns

- **Unique constraints**: Both `name` and `code` fields on main entities
- **Soft deletes**: All domain models support soft deletion
- **JSON fields**: Complex data (objectives, outcomes) stored as JSON
- **Sort ordering**: `sort_order` + `name` for consistent ordering
- **Status flags**: `is_active` boolean for entity state management

## File Storage

- **Public disk**: Logo uploads go to `storage/app/public/images/logos/`
- **Image processing**: Filament FileUpload with `imageEditor()`, 1:1 crop ratio, 1000x1000 target

## Key Reference Files

- **Filament setup**: `app/Providers/Filament/AdminPanelProvider.php`
- **Model example**: `app/Models/College.php` (shows all standard patterns)
- **Resource example**: `app/Filament/Admin/Resources/Colleges/` (complete structure)
- **Livewire auth**: `app/Livewire/Auth/Login.php` (validation patterns)

## Testing

- **Framework**: Pest PHP with Laravel integration
- **Config**: Test setup in `tests/Pest.php`
- **Database**: Uses SQLite in-memory for testing
- **Command**: Use `composer test` which runs config:clear + tests

## Important Notes

- Admin panel accessible at `/admin`
- Auto-discovery enabled for Resources/Pages/Widgets in `app/Filament/Admin/`
- Soft-deleted records accessible via `getRecordRouteBindingEloquentQuery()`
- When adding new features, follow the established modular patterns rather than putting everything in main resource files