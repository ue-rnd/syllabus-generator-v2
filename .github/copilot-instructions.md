# Syllabus Generator v2 - AI Coding Instructions

## Architecture Overview

This is a **Laravel + Livewire + Filament** application for managing academic institutions (colleges, departments, programs, courses) with a syllabus generation focus.

### Core Technology Stack
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Livewire with Flux UI components, Tailwind CSS 4.0
- **Admin Panel**: Filament 4.0 with custom resource organization
- **Build**: Vite with custom Tailwind integration
- **Testing**: Pest PHP testing framework
- **Database**: SQLite (development), soft deletes enabled

## Critical Patterns & Conventions

### Filament Resource Architecture
Resources follow a **modular schema pattern** - **never put forms/tables directly in Resource classes**:

```php
// ✅ Correct: app/Filament/Admin/Resources/Colleges/CollegeResource.php
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
All domain models use consistent patterns:
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

### Development Workflow

**Primary commands** (use composer scripts):
```bash
composer dev    # Starts server + queue + vite concurrently
composer test   # Runs config:clear + tests
```

**Filament specifics**:
- Admin panel at `/admin` (configured in `AdminPanelProvider`)
- Auto-discovery enabled for Resources/Pages/Widgets in `app/Filament/Admin/`
- Soft-deleted records accessible via `getRecordRouteBindingEloquentQuery()`

### Livewire Components
- **Auth components**: Pre-built in `app/Livewire/Auth/` with proper validation attributes
- **Layout**: Uses `#[Layout('components.layouts.auth')]` attribute
- **Validation**: Uses `#[Validate()]` attributes, not validate() rules
- **Navigation**: Uses `redirectIntended()` with `navigate: true`

### File Storage
- **Public disk**: Logo uploads go to `storage/app/public/images/logos/`
- **Image processing**: Filament FileUpload with `imageEditor()`, 1:1 crop ratio, 1000x1000 target

### Database Patterns
- **Unique constraints**: Both `name` and `code` fields on main entities
- **Soft deletes**: All domain models support soft deletion
- **JSON fields**: Complex data (objectives, outcomes) stored as JSON
- **Sort ordering**: `sort_order` + `name` for consistent ordering
- **Status flags**: `is_active` boolean for entity state management

## Key Files to Reference
- **Filament setup**: `app/Providers/Filament/AdminPanelProvider.php`
- **Model example**: `app/Models/College.php` (shows all standard patterns)
- **Resource example**: `app/Filament/Admin/Resources/Colleges/` (complete structure)
- **Livewire auth**: `app/Livewire/Auth/Login.php` (validation patterns)
- **Migration example**: `database/migrations/2025_09_12_045932_create_colleges_table.php`

## Testing
- **Framework**: Pest PHP with Laravel integration
- **Config**: Test setup in `tests/Pest.php`
- **Database**: Commented RefreshDatabase trait (enable as needed)

When adding new features, follow the established modular patterns rather than putting everything in main resource files.
