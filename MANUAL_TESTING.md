# Manual Testing Checklist

## 1. User Authentication

### Registration Testing
- [ ] Navigate to `/register`
- [ ] Test Form Validation:
  - [ ] Submit empty form - should show validation errors
  - [ ] Enter invalid email format - should show email error
  - [ ] Enter password less than 6 characters - should show password error
  - [ ] Submit without accepting terms - should show terms error
- [ ] Test Successful Registration:
  - [ ] Fill all fields correctly
  - [ ] Should redirect to login page
  - [ ] Should show success message
- [ ] Test Duplicate Email:
  - [ ] Try registering with an existing email
  - [ ] Should show email already exists error

### Login Testing
- [ ] Navigate to `/login`
- [ ] Test Form Validation:
  - [ ] Submit empty form - should show validation errors
  - [ ] Submit with invalid credentials - should show invalid credentials error
- [ ] Test Successful Login:
  - [ ] Login with valid credentials
  - [ ] Should redirect to homepage
  - [ ] User menu should appear with correct name
- [ ] Test "Remember Me":
  - [ ] Login with "Remember Me" checked
  - [ ] Close browser and reopen
  - [ ] Should still be logged in
- [ ] Test CSRF Protection:
  - [ ] Login should have CSRF token
  - [ ] Form should fail if token is invalid

### Logout Testing
- [ ] Click logout button
- [ ] Should redirect to homepage
- [ ] User menu should disappear
- [ ] Protected pages should redirect to login

## 2. User Profile Management

### Profile View
- [ ] Navigate to profile page
- [ ] Verify all information displays correctly:
  - [ ] Email (should be read-only)
  - [ ] First Name
  - [ ] Last Name
  - [ ] Phone Number
  - [ ] Address
  - [ ] Date of Birth
  - [ ] Nationality
  - [ ] Passport Number

### Profile Edit
- [ ] Click edit profile button
- [ ] Test Form Validation:
  - [ ] Submit with empty required fields - should show errors
  - [ ] Enter invalid phone number format - should show error
- [ ] Test Successful Update:
  - [ ] Update all editable fields
  - [ ] Submit form
  - [ ] Changes should persist
  - [ ] Should show success message
- [ ] Verify email cannot be changed

## 3. Admin Interface

### Dashboard
- [ ] Login as admin
- [ ] Navigate to `/admin`
- [ ] Verify Statistics Display:
  - [ ] Total Users count is accurate
  - [ ] Total Packages count is accurate
  - [ ] Total Bookings count is accurate
- [ ] Recent Bookings Table:
  - [ ] Shows latest 5 bookings
  - [ ] All columns display correctly
  - [ ] Status badges show correct colors

### User Management
- [ ] Navigate to user management section
- [ ] Test Table Functionality:
  - [ ] Sorting by columns works
  - [ ] Search functionality works
  - [ ] Pagination works
- [ ] Test User Edit:
  - [ ] Can edit user details
  - [ ] Can change user roles
  - [ ] Changes persist after save
- [ ] Test User Delete:
  - [ ] Can delete regular users
  - [ ] Cannot delete admin users
  - [ ] Confirmation dialog works
- [ ] Test Export:
  - [ ] Can export to CSV
  - [ ] Can export to Excel
  - [ ] Exported data is correct

### Package Management
- [ ] Navigate to package management section
- [ ] Test Add Package:
  - [ ] Can upload images
  - [ ] All fields validate correctly
  - [ ] Package appears in list after adding
- [ ] Test Edit Package:
  - [ ] Can modify all fields
  - [ ] Can change images
  - [ ] Changes persist after save
- [ ] Test Delete Package:
  - [ ] Delete confirmation works
  - [ ] Package removed from list
  - [ ] Related bookings handled correctly
- [ ] Test Active/Inactive Toggle:
  - [ ] Can toggle package status
  - [ ] Status updates immediately
  - [ ] Status persists after page refresh

### Booking Management
- [ ] Navigate to booking management section
- [ ] Test Filters:
  - [ ] Status filter works
  - [ ] Date range filter works
  - [ ] Search functionality works
- [ ] Test Booking View:
  - [ ] All booking details display correctly
  - [ ] User information is correct
  - [ ] Package information is correct
- [ ] Test Booking Edit:
  - [ ] Can change status
  - [ ] Can update travel dates
  - [ ] Can add/edit special requests
- [ ] Test Export:
  - [ ] Can export with different formats
  - [ ] Can export filtered results
  - [ ] Export includes all relevant fields

## 4. Security Testing

### Access Control
- [ ] Test as Regular User:
  - [ ] Cannot access admin pages
  - [ ] Cannot access other users' profiles
  - [ ] Cannot modify other users' bookings
- [ ] Test as Admin:
  - [ ] Can access all admin pages
  - [ ] Can view/edit all users
  - [ ] Can manage all bookings
- [ ] Test as Guest:
  - [ ] Cannot access protected pages
  - [ ] Redirected to login when needed

### Form Security
- [ ] Test XSS Prevention:
  - [ ] Try entering HTML/JS in text fields
  - [ ] Content should be escaped properly
- [ ] Test CSRF Protection:
  - [ ] All forms should have CSRF tokens
  - [ ] Forms should fail with invalid tokens

## 5. UI/UX Testing

### Responsive Design
- [ ] Test on Desktop (1920x1080):
  - [ ] All elements properly aligned
  - [ ] No horizontal scrolling
  - [ ] Tables display correctly
- [ ] Test on Laptop (1366x768):
  - [ ] Layout adjusts properly
  - [ ] All content readable
- [ ] Test on Tablet (768x1024):
  - [ ] Menu collapses correctly
  - [ ] Tables become scrollable
  - [ ] Forms remain usable
- [ ] Test on Mobile (375x667):
  - [ ] Menu works on mobile
  - [ ] Content stacks properly
  - [ ] All functions accessible

### User Experience
- [ ] Navigation:
  - [ ] Menu items clearly visible
  - [ ] Current page highlighted
  - [ ] Breadcrumbs work correctly
- [ ] Forms:
  - [ ] Error messages clear and visible
  - [ ] Success messages obvious
  - [ ] Required fields marked
- [ ] Tables:
  - [ ] Sorting indicators visible
  - [ ] Pagination clear and usable
  - [ ] Search/filter options obvious

## Issue Reporting Template

When finding issues, record them in the following format:

### Issue Template
```
Feature: [Feature Name]
Test Case: [Test Case Number]
Severity: [High/Medium/Low]
Description: [Detailed description of the issue]
Steps to Reproduce:
1. Step 1
2. Step 2
3. Step 3
Expected Result: [What should happen]
Actual Result: [What actually happened]
Screenshots: [If applicable]
Environment: [Browser, screen size, etc.]
```

## Testing Status

Use this checklist to track testing progress. Mark items as:
- ✅ Passed
- ❌ Failed (Include issue number)
- ⏳ Not Tested Yet
