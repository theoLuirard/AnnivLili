# Laravel Application Features

## Overview
A complete Laravel application with user authentication, profile management, and admin panel.

## Running the Application

The application is currently running on `http://127.0.0.1:8001`

### Default Test Accounts

**Admin User:**
- Email: `admin@example.com`
- Password: `password`
- Role: admin

**Regular User:**
- Email: `user@example.com`
- Password: `password`
- Role: user

## Features Implemented

### 1. Authentication System
- Login page with email/password
- Registration page with name, email, password confirmation
- Secure password hashing with bcrypt
- Session management with logout functionality

### 2. User Profile Management
- Profile page displaying user information
- Edit profile page allowing users to:
  - Update their full name
  - Set/update nickname
  - Upload and change profile picture
  - View account creation date
- Profile pictures stored in `storage/app/public/profiles/`

### 3. Role-Based Access Control (using Spatie Permission)
- Admin role for site administrators
- User role for regular users
- Middleware-protected routes
- Role assignment during registration

### 4. Admin Panel Features
- **User Management Page** (`/admin/users`)
  - Table displaying all users with pagination (10 per page)
  - Search functionality (search by name, email, or nickname)
  - View profile pictures in the list
  - Quick access to edit each user

- **User Edit Page** (`/admin/user/{id}`)
  - Edit user name, email, and nickname
  - Upload/update user profile picture
  - Delete user accounts
  - Confirmation dialogs for destructive actions

### 5. Navigation
- All pages include navigation bar with:
  - Quick access to Dashboard
  - Link to My Profile
  - Admin Panel link (only visible to admins)
  - Logout button

## File Structure

```
app/
 Http/Controllers/  
 Auth/     
 LoginController.php        
 RegisterController.php        
 ProfileController.php     
 AdminController.php     
 Models/  
 User.php (with Spatie HasRoles trait)      

resources/views/
 auth/  
 login.blade.php     
 register.blade.php     
 profile/  
 show.blade.php     
 edit.blade.php     
 admin/  
 users.blade.php     
 user-detail.blade.php     
 dashboard.blade.php  
 welcome.blade.php  

database/migrations/
 2026_06_03_214001_create_permission_tables.php  
 2026_06_03_214005_add_profile_fields_to_users_table.php  

database/seeders/
 CreateAdminRoleSeeder.php  
```

## Database Schema

### Users Table
- id
- name (full name)
- email (unique)
- password (hashed)
- nickname (optional)
- profile_picture (optional, path to image)
- created_at, updated_at

### Roles (Spatie Permission)
- admin
- user

### Model Has Roles
- Links users to their roles

## Technologies Used
- Laravel 13
- Spatie Laravel Permission
- Tailwind CSS for styling
- SQLite database
- Laravel Blade templating

## Security Features
- CSRF protection on all forms
- Password hashing with bcrypt
- Session-based authentication
- Role-based middleware protection
- File upload validation

## Next Steps (Optional Enhancements)
- Email verification
- Password reset functionality
- User ban/deactivate feature
- Activity logging
- Permission-based routes
- User profile pictures display on dashboard
- User roles/permissions management in admin panel
