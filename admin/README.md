# Research Portal Admin Interface

This folder contains the administrative interface for the Research Portal application. The admin interface allows administrators to manage applications, update statuses, export data, and configure system settings.

## Features

- **Modern Dashboard**: A responsive dashboard with statistics cards, filtering, and search capabilities
- **Application Management**: View, filter, and manage applications by status
- **Status Monitoring**: Tools to ensure data integrity and fix status-related issues
- **Data Export**: Export application data to Excel format
- **System Settings**: Configure system-wide settings with a user-friendly interface

## Technical Details

### Files Overview

- **header.php**: Common header with navigation sidebar for all admin pages
- **footer.php**: Common footer with shared JavaScript functionality
- **dashboard.php**: Main admin dashboard with application listing and management
- **status_monitor.php**: Tools for monitoring and fixing application status issues
- **settings.php**: System-wide settings configuration
- **export_applications.php**: Tool for exporting application data
- **change_password.php**: API endpoint for changing admin password

### Style Guide

The admin interface follows these style guidelines:

- Primary color: #007b55 (dark green)
- Secondary color: #6c757d (gray)
- Font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
- Icons: Bootstrap Icons

### JavaScript Features

- Collapsible sidebar with state persistence using localStorage
- Auto-dismiss alerts after 5 seconds
- Mobile-responsive design with adaptive sidebar
- Password change functionality with client-side validation

### Security Features

- Session-based authentication
- Password hashing using PHP's password_hash and password_verify
- Form validation on both client and server sides
- CSRF protection

## Usage

1. Login through the admin login page
2. Navigate using the sidebar menu
3. Use dashboard filters to view applications by status
4. Use the status monitor to identify and fix data issues
5. Configure system settings as needed

## Responsive Design

The admin interface is fully responsive and works on:
- Desktop (1200px+)
- Laptop (992px-1199px)
- Tablet (768px-991px)
- Mobile (below 768px)

### Mobile-Specific Features

- **Adaptive Card Layout**: On mobile devices, the applications table transforms into a card-based layout for better readability
- **Optimized Quick Actions**: Action buttons are reorganized for touch interfaces
- **Collapsible Navigation**: The sidebar collapses to provide more screen space for content
- **Stacked Forms**: Input forms and filters reorganize into a vertical layout
- **Touch-Friendly Buttons**: Larger touch targets for buttons and interactive elements
- **Responsive Statistics**: Stat cards stack and resize appropriately for smaller screens

## Accessibility

The interface includes:
- Proper contrast ratios for text
- ARIA labels for interactive elements
- Keyboard navigation support
- Screen reader-friendly markup
