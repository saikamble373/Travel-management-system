# Travel Management System

## Overview

Travel Management System is a web-based platform that allows users to explore travel packages, make bookings, manage travel history, and interact with an AI-powered travel assistant. The system also provides an administrative dashboard for managing packages, bookings, users, enquiries, and analytics.

## Features

### User Features

* User Registration and Login
* Browse Travel Packages
* Package Details View
* Online Package Booking
* Travel History Management
* Raise Support Tickets
* Wishlist Management
* Invoice Generation
* Profile Management
* AI Travel Assistant

### Admin Features

* Secure Admin Login
* Dashboard with Statistics
* Package Management
* User Management
* Booking Management
* Enquiry Management
* Issue Management
* Content/Page Management
* AI Analytics Dashboard
* Profile and Password Management

### AI Features

* AI-powered Travel Chatbot
* Intelligent User Assistance
* Travel Recommendations
* Automated Query Support

## Technology Stack

### Frontend

* HTML
* CSS
* JavaScript
* Bootstrap

### Backend

* PHP

### Database

* MySQL

### AI Integration

* Python
* AI Service Module

### Server Environment

* XAMPP
* Apache
* MySQL

## Project Structure

```text
tms/
├── admin/
├── css/
├── includes/
├── images/
├── ai_service.py
├── enquiry.php
├── invoice.php
├── wishlist.php
├── package-list.php
├── package-details.php
├── profile.php
├── index.php
└── requirements.txt
```

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/soham-5656/travel-management-system.git
cd travel-management-system
```

### 2. Setup XAMPP

* Install XAMPP
* Start Apache
* Start MySQL

### 3. Copy Project

Move the project folder into:

```text
xampp/htdocs/
```

### 4. Create Database

1. Open phpMyAdmin
2. Create a database
3. Import the provided SQL file

### 5. Configure Database

Update database credentials inside the project configuration files:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "travel_management";
```

### 6. Run Application

Open:

```text
http://localhost/tms
```

## Modules

### User Module

* Registration
* Authentication
* Package Booking
* Travel History
* Wishlist
* Support Tickets

### Admin Module

* Dashboard
* Package Management
* Booking Management
* User Management
* Analytics

### AI Module

* AI Chatbot
* Query Handling
* Travel Assistance

## Future Enhancements

* Online Payment Gateway
* Hotel Booking Integration
* Flight Booking Integration
* Email Notifications
* Mobile Application
* Recommendation Engine
* Multi-language Support

## Author

Soham Dawale 
Sai Kambale 

## License

This project is developed for educational and learning purposes.
