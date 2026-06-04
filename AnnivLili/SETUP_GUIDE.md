# Laravel Application - Setup & Usage Guide

## Project Overview

A fully functional Laravel 13 application with:
- User authentication (login/register)
- User profile management with picture uploads
- Role-based admin panel
- User management system with search functionality

## Current Status

 Application is running at **http://127.0.0.1:8000**

## Accessing the Application

### Test Accounts

**Admin Account:**
- Email: `admin@example.com`
- Password: `password`
- Access: Full application + Admin Panel

**Regular User Account:**
- Email: `user@example.com`
- Password: `password`
- Access: Application only (no admin features)

## Navigation Guide

### For Regular Users

1. **Home Page** (`/`)
   - Welcome page with Login/Register options

2. **Login** (`/login`)
   - Email and password authentication

3. **Register** (`/register`)
   - Create new user account with name, email, password

4. **Dashboard** (`/dashboard`)
   - Welcome message after login
   - Shows user info and account creation date

5. **My Profile** (`/profile`)
   - View profile information
   - Profile picture display
   - Link to edit profile

6. **Edit Profile** (`/profile/edit`)
   - Update full name
   - Update nickname
   - Upload/change profile picture
   - Profile pictures are securely stored and served via public storage

### For Admin Users

1. **Admin Panel** (`/admin/users`)
   - View all users in a paginated table (10 per page)
   - Search users by name, email, or nickname
   - View profile pictures in list
   - Click "View/Edit" to manage individual users

2. **User Management** (`/admin/user/{id}`)
   - Edit user information (name, email, nickname)
   - Upload/update user profile picture
   - Delete user accounts
   - Confirmation dialogs prevent accidental deletion

## Database

The application uses SQLite with the following tables:

- `users` - User accounts (with nickname and profile_picture fields)
- `roles` - User roles (admin, user)
- `model_has_roles` - User-to-role assignments
- `permissions` - Permission definitions
- Additional Spatie permission tables

## Features Detail

### 1. Authentication
- Secure login with email/password
- Password hashing with bcrypt
- Session-based authentication
- CSRF protection on all forms

### 2. Profile Management
- Edit name and nickname
- Upload profile pictures (max 2MB, JPEG/PNG/GIF)
- Pictures stored in `storage/app/public/profiles/`
- Accessible via `/storage/` route

### 3. Admin Management
- Complete user CRUD operations
- Pagination support (10 users per page)
- Full-text search across user fields
- Admin-only access with role middleware

## File Locations

```
app/Http/Controllers/
 Auth/LoginController.php  
 Auth/RegisterController.php  
 ProfileController.php  
 AdminController.php  

resources/views/
 welcome.blade.php  
 dashboard.blade.php  
 auth/login.blade.php  
 auth/register.blade.php  
 profile/show.blade.php  
 profile/edit.blade.php  
 admin/users.blade.php  
 admin/user-detail.blade.php  

database/
 migrations/  
 2026_06_03_214001_create_permission_tables.php     
 2026_06_03_214005_add_profile_fields_to_users_table.php     
 seeders/  
 CreateAdminRoleSeeder.php      
```

## Available Commands

```bash
# Start development server (already running on port 8000)
php artisan serve

# Run migrations
php artisan migrate

# Run seeders (creates roles and test users)
php artisan db:seed --class=CreateAdminRoleSeeder

# Create storage link for public uploads
php artisan storage:link

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## Security Features

 CSRF protection on all forms
 Password hashing with bcrypt
 Session-based authentication
 Role-based route middleware
 File upload validation
 SQL injection protection (Eloquent ORM)
 XSS prevention in Blade templates

## Testing the Admin Panel

1. Go to http://127.0.0.1:8000/login
2. Enter credentials: admin@example.com / password
3. Click "Admin Panel" in the navigation
4. See all users with search and edit options

## Styling

- Uses Tailwind CSS CDN for styling
- Responsive design with mobile support
- Clean, modern UI with good UX

## Next Steps / Enhancements

- Add email verification
- Add password reset functionality
- Add user activity logging
- Add role/permission management in admin panel
- Add export users to CSV
- Add user status (active/inactive/banned)
- Add email notifications
- Add two-factor authentication

---

**Created:** June 3, 2026
**Technology Stack:** Laravel 13, Spatie Permission, Tailwind CSS, SQLite
