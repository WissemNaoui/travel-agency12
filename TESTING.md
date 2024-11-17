# Travel Agency Application Testing Guide

This document outlines the testing procedures for various features of the travel agency application. Follow these steps to ensure all functionality works as expected.

## 1. User Authentication Testing

### 1.1 Registration
1. Navigate to `/register`
2. Test form validation:
   - Try submitting empty form
   - Try submitting with invalid email format
   - Try submitting with password less than 6 characters
   - Try submitting without accepting terms
3. Test successful registration:
   - Fill all fields correctly
   - Verify redirect to login page
   - Verify success flash message
4. Test duplicate email:
   - Try registering with an existing email
   - Verify error message

### 1.2 Login
1. Navigate to `/login`
2. Test form validation:
   - Try submitting empty form
   - Try submitting with invalid credentials
3. Test successful login:
   - Login with valid credentials
   - Verify redirect to homepage
   - Verify user menu appears
4. Test "Remember Me":
   - Login with "Remember Me" checked
   - Close browser and reopen
   - Verify still logged in
5. Test CSRF protection:
   - Modify CSRF token in browser
   - Verify form submission fails

### 1.3 Logout
1. Click logout button
2. Verify redirect to homepage
3. Verify user menu disappears
4. Try accessing protected pages
5. Verify redirect to login

## 2. User Profile Testing

### 2.1 Profile View
1. Login and navigate to profile
2. Verify all user information displays correctly:
   - Email (read-only)
   - First Name
   - Last Name
   - Phone Number
   - Address
   - Date of Birth
   - Nationality
   - Passport Number

### 2.2 Profile Edit
1. Click edit profile button
2. Test form validation:
   - Try submitting with empty required fields
   - Try invalid phone number format
3. Test successful update:
   - Modify all editable fields
   - Submit form
   - Verify changes persist
   - Verify success message
4. Verify email remains unchanged

## 3. Admin Interface Testing

### 3.1 Dashboard
1. Login as admin
2. Verify statistics display:
   - Total Users count
   - Total Packages count
   - Total Bookings count
3. Verify recent bookings table:
   - Shows latest 5 bookings
   - All columns display correctly
   - Status badges show correct colors

### 3.2 User Management
1. Navigate to user management
2. Test table functionality:
   - Sorting by columns
   - Search functionality
   - Pagination
3. Test user edit:
   - Edit user details
   - Change user roles
   - Verify changes persist
4. Test user delete:
   - Delete non-admin user
   - Verify admin users can't be deleted
5. Test export:
   - Export to CSV
   - Export to Excel
   - Verify file format and content

### 3.3 Package Management
1. Navigate to package management
2. Test add package:
   - Upload image
   - Fill all fields
   - Verify validation
   - Verify success
3. Test edit package:
   - Modify all fields
   - Change image
   - Verify changes persist
4. Test delete package:
   - Delete package
   - Verify related bookings handling
5. Test active/inactive toggle:
   - Toggle package status
   - Verify display updates

### 3.4 Booking Management
1. Navigate to booking management
2. Test table filters:
   - Filter by status
   - Filter by date range
   - Search functionality
3. Test booking view:
   - View booking details
   - Verify all information displays
4. Test booking edit:
   - Change status
   - Change travel date
   - Add special requests
5. Test export:
   - Export with different formats
   - Export with date range
   - Export with status filter

## 4. Security Testing

### 4.1 Access Control
1. Test unauthorized access:
   - Try accessing admin pages as regular user
   - Try accessing profile when logged out
2. Test CSRF protection:
   - Modify form tokens
   - Verify protection works
3. Test remember me security:
   - Verify token rotation
   - Test expiration

### 4.2 Form Security
1. Test XSS protection:
   - Try submitting HTML/JS in text fields
   - Verify proper escaping
2. Test SQL injection:
   - Try SQL patterns in search fields
   - Verify parameterized queries work

## 5. Performance Testing

### 5.1 Page Load
1. Test initial page load times
2. Test admin table load with large datasets
3. Test image load times in package gallery

### 5.2 Form Submission
1. Test multiple concurrent form submissions
2. Test file upload with large images
3. Test export with large datasets

## 6. Browser Testing

Test all features in:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## 7. Responsive Design Testing

Test all pages at:
- Desktop (1920x1080)
- Laptop (1366x768)
- Tablet (768x1024)
- Mobile (375x667)

## Issue Reporting

When reporting issues:
1. Specify the test case number
2. Describe the expected behavior
3. Describe the actual behavior
4. Include browser/device details
5. Add screenshots if applicable
6. List steps to reproduce

## Testing Environment

- Symfony Version: 6.4 LTS
- PHP Version: 8.3.6
- MySQL Version: Latest
- Testing Database: Separate from development
- Browser: Latest versions
- Device: Both desktop and mobile
