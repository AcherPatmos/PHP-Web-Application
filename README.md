# Cerebro Hero Management System

A PHP and MySQL hero registry. The roster is public; creating, editing, and
deleting records requires a staff login. Staff can also create further staff
accounts.

## Requirements

- **XAMPP** (or any Apache + MySQL + PHP stack)
- **PHP 7.4 or newer** the code uses typed function signatures, the null
  coalescing operator, and `mysqli::get_result()`. XAMPP ships with a new
  enough version by default.
- **Git**, if you are cloning rather than copying the folder.

## Cloning and running it

Clone straight into your web root, `htdocs` because that is the only place Apache serves. 

```bash
cd /path/to/xampp/htdocs
git clone <repository-url> "PHP WEB APPLICATION"
```

Then, in order:

1. **Start Apache and MySQL** from the XAMPP control panel.
   
2. **Check the database login.** `PHP/db.php` is set to the XAMPP defaults —
   user `root`, empty password, database `hero_db`. If your MySQL uses
   different credentials, edit the four variables at the top of that file.
  
3. **Create the database.** In phpMyAdmin, use the **Import** tab to run
   `Database file/heroes.sql`, then `Database file/insert_heroes.sql`.
   The first file drops and recreates `hero_db`, so any existing data is lost.

4. **Open the site** at
   `http://localhost/PHP%20WEB%20APPLICATION/PHP/index.php`
   
5. **Login by** by visiting
   `http://localhost/PHP%20WEB%20APPLICATION/PHP/login.php`.

## File structure

Every PHP file lives in `PHP/`, both stylesheets live in `css/`, and the SQL
lives in `Database file/`. Paths in the code are written to match this exactly,
so moving a file means editing the paths inside it.

```
PHP WEB APPLICATION/
├── .gitignore
├── css/
│   ├── login.css            login + first-time setup screens ONLY
│   └── style.css            roster, hero page, every staff screen
├── Database file/
│   ├── heroes.sql           schema — import FIRST
│   └── insert_heroes.sql    starting roster — import SECOND
├── images/
│   ├── wolverine.jpeg       login page picture    
├── js/
│   └── validation.js        login form checks
└── PHP/
    ├── auth.php             sessions + helper functions
    ├── db.php               database connection
    ├── index.php            public roster — the site entry point
    ├── hero.php             one hero's full case file
    ├── login.php
    ├── login_process.php
    ├── logout.php
    ├── setup_staff.php      creates the FIRST account, then locks itself
    ├── manage_heroes.php
    ├── add_hero.php
    ├── edit_hero.php
    ├── delete_hero.php
    ├── manage_staff.php     list accounts
    ├── add_staff.php        create more accounts
    └── delete_staff.php
```


