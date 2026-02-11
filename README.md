# Supplier System

A simple **PHP & MySQL based Supplier & Expense Management System** for managing suppliers, invoices, expense types, and viewing monthly totals.

## Features
- Add, edit, and delete suppliers  
- Create, search, and manage invoices  
- Manage expense types  
- View monthly expenses and totals  
- Simple and clean interface using PHP, HTML, and CSS  

## Tech Stack
- **Backend:** PHP  
- **Database:** MySQL  
- **Frontend:** HTML, CSS  

## Repository Structure
```
├── main.php                  # Dashboard / Homepage
├── manage_suppliers.php      # Add/Edit/Delete suppliers
├── search_invoices.php       # Search invoices
├── view_monthly_expenses.php # View monthly expenses
├── manage_expense_types.php  # Manage expense types
├── styles.css                # Basic CSS styles
├── supplier_system.sql       # Database schema
└── logo.png                  # Project logo
```

## Installation & Setup

### 1. Clone the repository
```bash
git clone https://github.com/Ahmed-Mostafa20/supplier_system.git
```

### 2. Create Database
Import the provided SQL file into MySQL:
```sql
SOURCE path/to/supplier_system.sql;
```

### 3. Configure Database Connection
Update the database credentials in PHP files if needed (host, username, password).

### 4. Run the System
Place the project folder in your server's `htdocs` (for XAMPP) and open in browser:
```
http://localhost/supplier_system/main.php
```

## Usage
- **Suppliers** – Manage supplier list  
- **Invoices** – Create, edit, search invoices  
- **Expense Types** – Add or remove expense categories  
- **Reports** – View monthly expense totals  

## Contributing
Feel free to fork, improve the UI, or add new features. Pull requests are welcome.

## License
Open source – no specific license provided.

---

## Optional Notes for Setup
- Make sure **Apache and MySQL** are running if using XAMPP/WAMP.  
- Database file `supplier_system.sql` contains tables: `suppliers`, `invoices`, `expenses`.  
- `styles.css` is used for basic styling.  
- You can add screenshots or logos under `logo.png` for better presentation.
