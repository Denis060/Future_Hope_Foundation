# Future Hope Foundation Website

This is a complete PHP website for the Future Hope Foundation, a nonprofit charitable organization. The website includes both frontend and backend functionality.

## Features

- **Frontend**: Public-facing website with information about the foundation's services, events, projects, etc.
- **Backend**: Admin panel to manage website content, services, events, etc.

## Technical Stack

- **PHP**: Backend programming language
- **MySQL**: Database
- **Bootstrap 5.2.3**: Frontend framework for responsive design
- **JavaScript/jQuery**: Client-side functionality
- **Font Awesome**: Icons
- **Owl Carousel**: For sliders and carousels
- **Summernote**: Rich text editor for the admin panel
- **DataTables**: For data tables in the admin panel

## Setup Instructions

1. **Database Configuration**:
   - Database configuration can be found in `includes/config.php`
   - Default database credentials:
     - Host: localhost
     - User: root
     - Password: [PASSWORD REMOVED]
     - Database: futurehope_db

2. **Admin Access**:
   - URL: `/admin/`
   - Default credentials:
     - Username: admin
     - Password: admin123

3. **Website Structure**:
   - `index.php`: Home page
   - `about.php`: About page
   - `services.php`: Services page
   - `contact.php`: Contact page
   - `admin/`: Admin panel directory

4. **Key Directories**:
   - `includes/`: Contains configuration and function files
   - `assets/`: Contains CSS, JavaScript, and image files
   - `uploads/`: Contains uploaded files (images, etc.)

5. **Development Server**:
   - You can run a local development server using PHP's built-in server:
     ```
     php -S localhost:8080
     ```
   - Or use a tool like Laragon, XAMPP, WAMP, etc.

## Initialization Scripts

- `initialize_services.php`: Initializes the services table with predefined services
- `update_logo.php`: Updates the site name and logo in the settings table

## Troubleshooting

If you encounter any issues:

1. Check the database connection in `includes/config.php`
2. Make sure all required tables are created (the script should create them automatically)
3. Check for any PHP errors in your server logs
4. Use the provided debug scripts (`debug.php`, `test_db.php`, etc.) to diagnose issues

## Features Overview

### Frontend

- **Home Page**: Featuring sliders, services, upcoming events, ongoing projects, testimonials, and team members
- **About Page**: Information about the foundation, mission, vision, etc.
- **Services Page**: Listing of all services provided by the foundation
- **Service Detail Page**: Detailed information about each service
- **Contact Page**: Contact form and information

### Backend (Admin Panel)

- **Dashboard**: Overview of website statistics
- **Services Management**: Add, edit, delete services
- **Settings**: Update website settings (site name, logo, contact information, etc.)

## Credits

Future Hope Foundation Website - A PHP-based nonprofit organization website.
