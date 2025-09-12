# Role and Permission Management Guide

This guide explains how to use the Spatie Laravel Permission package that has been set up in your syllabus generator application.

## Overview

The application now includes a comprehensive role and permission management system with:
- Pre-defined roles and permissions
- Console commands for management
- Web interface for easy management
- User assignment capabilities

## Pre-defined Roles

### Superadmin
- **Full system access** - Complete control over the entire system
- Can manage all users, roles, and permissions
- Can access all features and settings
- Typically reserved for system administrators

### Admin (College Deans)
- **College-level management** - Oversight of entire college operations
- Can manage users, roles, and permissions
- Can create, edit, and delete all content
- Can view analytics and reports
- Can assign roles to users

### Faculty
- **Teaching staff** - Core academic personnel
- Can create and edit syllabi
- Can manage their courses
- Can view settings
- Limited to their own content management

## Pre-defined Permissions

### User Management
- `view users` - View user list
- `create users` - Create new users
- `edit users` - Edit existing users
- `delete users` - Delete users

### Role Management
- `view roles` - View roles
- `create roles` - Create new roles
- `edit roles` - Edit existing roles
- `delete roles` - Delete roles
- `assign roles` - Assign roles to users

### Permission Management
- `view permissions` - View permissions
- `create permissions` - Create new permissions
- `edit permissions` - Edit existing permissions
- `delete permissions` - Delete permissions
- `assign permissions` - Assign permissions to users

### Syllabus Management
- `view syllabus` - View syllabus
- `create syllabus` - Create new syllabus
- `edit syllabus` - Edit existing syllabus
- `delete syllabus` - Delete syllabus
- `publish syllabus` - Publish syllabus

### Course Management
- `view courses` - View courses
- `create courses` - Create new courses
- `edit courses` - Edit existing courses
- `delete courses` - Delete courses

### Settings Management
- `view settings` - View settings
- `edit settings` - Edit settings

### Dashboard Access
- `view dashboard` - Access dashboard
- `view analytics` - View analytics

## Console Commands

### List Roles
```bash
php artisan roles:manage list-roles
```

### List Permissions
```bash
php artisan roles:manage list-permissions
```

### Create a Role
```bash
php artisan roles:manage create-role --name="editor" --permissions="view syllabus,edit syllabus"
```

### Create a Permission
```bash
php artisan roles:manage create-permission --name="manage content"
```

### Assign Role to User
```bash
php artisan roles:manage assign-role --user="user@example.com" --role="faculty"
php artisan roles:manage assign-role --user="dean@college.edu" --role="admin"
php artisan roles:manage assign-role --user="admin@college.edu" --role="superadmin"
```

### Assign Permission to User
```bash
php artisan roles:manage assign-permission --user="user@example.com" --name="view analytics"
```

### Remove Role from User
```bash
php artisan roles:manage remove-role --user="user@example.com" --role="instructor"
```

### Remove Permission from User
```bash
php artisan roles:manage remove-permission --user="user@example.com" --name="view analytics"
```

## Web Interface

Access the role management interface at `/roles` (requires `view roles` permission).

### Features
- **Roles Tab**: Create, edit, and delete roles
- **Permissions Tab**: Create, edit, and delete permissions
- **User Assignments Tab**: Assign roles and permissions to users
- **Search**: Search through roles, permissions, and users
- **Real-time Updates**: Changes are reflected immediately

## Usage in Code

### Check if User Has Role
```php
if ($user->hasRole('superadmin')) {
    // User is a superadmin
}

if ($user->hasRole('admin')) {
    // User is a college dean
}

if ($user->hasRole('faculty')) {
    // User is faculty
}
```

### Check if User Has Permission
```php
if ($user->can('edit syllabus')) {
    // User can edit syllabus
}
```

### Check Multiple Permissions
```php
if ($user->hasAnyPermission(['edit syllabus', 'create syllabus'])) {
    // User can edit or create syllabus
}
```

### Assign Role to User
```php
$user->assignRole('instructor');
```

### Assign Permission to User
```php
$user->givePermissionTo('edit syllabus');
```

### Remove Role from User
```php
$user->removeRole('instructor');
```

### Remove Permission from User
```php
$user->revokePermissionTo('edit syllabus');
```

## Middleware Usage

### Protect Routes with Permissions
```php
Route::get('/syllabus', function () {
    // Only users with 'view syllabus' permission can access
})->middleware('can:view syllabus');
```

### Protect Routes with Roles
```php
Route::get('/admin', function () {
    // Only users with 'admin' role can access
})->middleware('role:admin');
```

## Blade Directives

### Check Permission in Blade
```blade
@can('edit syllabus')
    <a href="/syllabus/edit">Edit Syllabus</a>
@endcan
```

### Check Role in Blade
```blade
@role('admin')
    <a href="/admin">Admin Panel</a>
@endrole
```

## Seeding Data

To seed the initial roles and permissions:

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=RolePermissionSeeder
```

Or run all seeders:
```bash
php artisan db:seed
```

## Customization

### Adding New Roles
1. Use the web interface at `/roles`
2. Or use the console command: `php artisan roles:manage create-role --name="new-role"`
3. Or add to `RoleSeeder.php` and run the seeder

### Adding New Permissions
1. Use the web interface at `/roles`
2. Or use the console command: `php artisan roles:manage create-permission --name="new-permission"`
3. Or add to `RoleSeeder.php` and run the seeder

### Modifying Role Permissions
1. Use the web interface to edit roles
2. Or use the console command to assign permissions to roles

## Security Notes

- Always use middleware to protect routes
- Check permissions before allowing actions
- Regularly audit user roles and permissions
- Use the principle of least privilege
- Test permission checks thoroughly

## Troubleshooting

### Permission Not Working
1. Check if the permission exists in the database
2. Verify the user has the permission (directly or through a role)
3. Clear the permission cache: `php artisan permission:cache-reset`
4. Check for typos in permission names

### Role Not Working
1. Check if the role exists in the database
2. Verify the user has the role assigned
3. Clear the permission cache: `php artisan permission:cache-reset`
4. Check for typos in role names

### Cache Issues
If you're experiencing issues with permissions not updating:
```bash
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
```
