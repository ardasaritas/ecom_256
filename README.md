
# 🛒 ExpirySaver – A Sustainability e-Commerce Platform

**Course:** CTIS 256 – Introduction to Backend Development  
**Term:** Spring 2025  
**Team Size:** 5 Members  
**Submission Deadline:** May 18, 2025  
**Demo:** May 19, 2025  

---

## 📌 Project Overview

ExpirySaver is a multi-user web-based application designed to reduce food waste by helping markets sell products nearing expiration at discounted prices. This project aims to create a win-win situation for both markets and consumers while promoting sustainability.

---

## 👥 User Roles

- **Market Users**
  - Register and manage store information
  - Add, edit, delete products with expiration tracking
  - View and mark expired items

- **Consumer Users**
  - Register and search for products by keywords
  - Filter by city and district
  - Add to cart, manage cart with AJAX, complete purchase

---

## 🛠 Technologies Used

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, Bootstrap, AJAX, jQuery
- **Security:** CSRF, XSS, SQL Injection protection, hashed passwords, input validation

---

## 📁 File Structure

```
├── app
│   ├── controllers            # Server-side logic, grouped by user role
│   │   ├── consumer
│   │   │   ├── cart.php
│   │   │   └── dashboard.php
│   │   └── market
│   │       ├── add_product.php
│   │       ├── dashboard.php
│   │       ├── delete_product.php
│   │       └── edit_product.php
│   ├── includes               # Core backend logic (auth, DB, helpers)
│   │   ├── auth.php
│   │   ├── csrf.php
│   │   ├── db.php
│   │   ├── email_config.php (initially in .gitignore)
│   │   ├── email.php
│   │   └── functions.php
│   ├── templates              # Shared HTML components
│   │   ├── footer.php
│   │   ├── header.php
│   │   └── navbar.php
│   ├── vendor (initially in .gitignore)
│   └── composer.json
│   └── composer.lock
│
├── public # Public-facing routes (DocumentRoot)
│   ├── ajax                
│   │   ├── purchase.php
│   │   └── update_cart.php         
│   ├── assets
│   │   └── style.css
│   ├── uploads
│   ├── about.php
│   ├── cart.php
│   ├── consumer_dashboard.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── market_dashboard.php
│   ├── profile.php
│   ├── register.php
│   ├── search.php
│   └── verify_email.php
└── sql                       # Database schema and seed data
    ├── schema.sql
    └── seed_data.sql
.gitignore
README.md
```

---

## ✅ Key Features

- Email-based registration with 6-digit confirmation codes
- Role-based access control with session tracking
- Secure login with hashed passwords
- Product expiration management and visibility
- Smart keyword search with filtering and pagination
- Persistent shopping cart with AJAX updates
- CSRF tokens and XSS/SQLi protection throughout
- Responsive UI using Bootstrap

---

## 🔐 Security Measures

- CSRF protection with unique tokens in all forms
- Input sanitization with `htmlspecialchars()` and server-side validation
- SQL injection prevention using prepared statements
- Session-based role checks for all restricted pages

---

## 🧪 Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/ardasaritas/expirysaver.git
   cd expirysaver
   ```

2. Import the database:
   - Run `sql/schema.sql` in phpMyAdmin or MySQL CLI
   - (Optional) Run `sql/seed_data.sql` for demo data

3. Configure database access in `/app/includes/db.php`:
   ```php
   $dsn = "mysql:host=localhost;dbname=expirySaver;charset=utf8mb4";
   $user = "root";
   $pass = ""; 
   ```

4. Install PHP dependencies 
   ```bash
   cd expirySaver/app 
   composer install 
   cd ..
   ```

5. Create and configure email credentials in expirySaver/app/includes/email_config.php
   ```bash
   cd expirySaver
   touch app/includes/email_config.php
   ```
   
   ```php
   define('EMAIL', 'your_email@gmail.com');
   define('PASSWORD', 'your_app_password'); // Use an App Password if using Gmail with 2FA
   define('FULLNAME', 'Expiry Saver');
   ```
7. Set your web server's root to the `/public` directory.

---

## 👨‍💻 Contributors

| Name                         | Role                                       |
|------------------------------|--------------------------------------------|
| Hamit Efe Eldem (@HamitEldem)| User Auth & Registration                   |
| Zeynep Arslan (@zearslan)    | Market Dashboard & Products                |
| Berke Anık (@berkeanik)      | Consumer Search & Pagination               |
| Arda Sarıtaş (@ardasaritas)  | UI/UX, Data, Sessions, AJAX, Shopping Cart |

---

## 📄 License

For academic use only. Do not distribute or reuse without permission from all group members.
