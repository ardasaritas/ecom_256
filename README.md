
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

- **Backend:** PHP (Procedural)
- **Database:** MySQL
- **Frontend:** HTML, CSS, Bootstrap, AJAX, jQuery
- **Security:** CSRF protection, hashed passwords, input validation

---

## 📁 File Structure

```
public/              # Public entry point and assets
├── index.php
├── login.php
├── register.php
├── logout.php
├── verify_email.php
├── assets/
│   ├── css/
│   ├── js/
│   └── img/

includes/            # Backend logic, DB, auth, security
├── db.php
├── config.php
├── auth.php
├── csrf.php
├── functions.php
├── email.php

templates/           # Reusable UI components
├── header.php
├── navbar.php
├── footer.php
├── form_components.php

market/              # Market dashboard and operations
├── dashboard.php
├── add_product.php
├── edit_product.php
├── delete_product.php

consumer/            # Consumer dashboard and product/cart flow
├── dashboard.php
├── search.php
├── cart.php

ajax/                # AJAX endpoints
├── update_cart.php
├── purchase.php
├── product_search.php

sql/                 # SQL schema and seed data
├── schema.sql
├── seed_data.sql

uploads/             # Uploaded product images (write-protected)

README.md
.gitignore
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
   git clone https://github.com/your-username/expirysaver.git
   cd expirysaver
   ```

2. Import the database:
   - Run `sql/schema.sql` in phpMyAdmin or MySQL CLI
   - (Optional) Run `sql/seed_data.sql` for demo data

3. Configure database access in `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'expirysaver');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. Set your web server's root to the `/public` directory.

---

## 👨‍💻 Contributors

| Name          | Role                      |
|---------------|---------------------------|
| [Dev]      | User Auth & Registration  |
| [Dev]      | Market Dashboard & Products |
| [Dev]      | Consumer Search & Pagination |
| [Dev]      | Shopping Cart & AJAX Logic |
| [Dev]      | Frontend Design & Validation |

---

## 📄 License

For academic use only. Do not distribute or reuse without permission from all group members.
