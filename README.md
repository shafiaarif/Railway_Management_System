# Railway Management System - Sageline Express

## Overview

Sageline Express is a Railway Management System developed using PHP, MySQL, HTML, CSS, JavaScript, AJAX, and TCPDF. The system provides an efficient platform for railway ticket booking, payment processing, ticket generation, and travel history management.

The project allows passengers to register, search for trains, book seats, make payments, download tickets in PDF format, and view their travel history. An admin module is also included for managing and viewing registered users.

---

## Features

### Passenger Features

* Passenger Registration
* Secure Login and Logout
* Passenger Dashboard
* Train Search and Timings
* Route Selection
* Seat Availability Checking
* Seat Booking
* Online Payment Processing
* Automatic Ticket Generation
* PDF Ticket Download
* View All Booked Tickets
* View Most Recent Ticket
* Travel History Tracking

### Admin Features

* Admin Dashboard
* View Registered Users
* Monitor Passenger Information

---

## Technologies Used

### Frontend

* HTML5
* CSS3
* JavaScript
* AJAX

### Backend

* PHP

### Database

* MySQL

### Additional Libraries

* TCPDF (PDF Ticket Generation)

---

## Database Tables

The system uses the following database tables:

* user
* passenger
* admin
* train
* station
* route
* seats
* seat_class
* booking
* payment
* ticket
* travel_history
* phonenumber

---

## Project Structure

```text
Railway-Management-System/
│
├── booking.php
├── login.php
├── logout.php
├── register_passenger.php
├── passenger_dashboard.php
├── payment.php
├── tickets.php
├── view_recent_ticket.php
├── view_history.php
├── view_user.php
├── train_timings.php
├── get_route_id.php
├── get_seats.php
├── insert_booking.php
├── store_session.php
├── download_ticket.php
├── seat_availability.php
├── db.php
│
├── images/
├── vendor/
└── railway.sql
```

---

## Installation

1. Clone the repository:

```bash
git clone https://github.com/yourusername/Railway-Management-System.git
```

2. Move the project folder to:

```text
xampp/htdocs/
```

3. Import the database:

* Open phpMyAdmin
* Create a database named:

```text
railway
```

* Import:

```text
railway.sql
```

4. Configure database connection inside:

```php
db.php
```

5. Start Apache and MySQL from XAMPP.

6. Open the project in your browser:

```text
http://localhost/Railway-Management-System/
```

---

## Ticket Generation

The system generates downloadable PDF tickets using the TCPDF library. Each ticket contains:

* Passenger Information
* Train Details
* Source and Destination
* Travel Date
* Seat Number
* Fare Information

---

## Future Improvements

* Ticket Cancellation Module
* Admin Analytics Dashboard
* Revenue Reports
* Email Notifications
* Real-Time Train Tracking
* Online Payment Gateway Integration

---

## Author

Shafia Arif

Undergraduate Student

Computer Information and Systems Engineering

NED University of Engineering and Technology

---

## License

This project was developed for academic and educational purposes.
