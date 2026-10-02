# CodeLog

CodeLog is a simple blog and content publishing web application built with PHP and MySQL. It allows visitors to browse posts by category, sign up and sign in, like content, leave comments, and gives admins a dashboard for creating and managing blog posts.

## Features

- Public blog homepage with category-based filtering
- Post detail page with full article content
- User registration and login
- Like and comment functionality on posts
- Admin panel for creating, editing, and deleting blog posts
- Image upload support for article thumbnails
- Responsive layout using PicoCSS and custom styles

## Tech Stack

- PHP 8+
- MySQL / MariaDB
- HTML5, CSS3, JavaScript
- mysqli for database access

## Project Structure

```text
codelog/
├── admin/
│   ├── allpost.php
│   ├── create.php
│   ├── delete.php
│   ├── edit.php
│   ├── footer.php
│   ├── header.php
│   ├── logout.php
│   ├── signin_admin.php
│   └── uploads/
├── assets/
├── config/
│   └── db.php
├── css/
│   └── styles.css
├── js/
│   └── scripts.js
├── blog.php
├── footer.php
├── header.php
├── index.php
├── logout.php
├── profile.php
├── signin.php
├── signup.php
├── codeLog.sql
├── README.md
└── .gitignore (if present)
```

## Database Setup

1. Create a MySQL database named `codeLog`.
2. Import the SQL file from the project root:

```bash
mysql -u root -p codeLog < codeLog.sql
```

3. Update the database credentials in `config/db.php` if needed:

```php
<?php
$host = 'localhost';
$db = 'codeLog';
$user = 'root';
$pass = '';
```

## Running the Project

1. Make sure Apache and MySQL are running.
2. Place the project folder inside your local web server root, such as:

```text
htdocs/codelog
```

3. Open the project in your browser:

```text
http://localhost/codelog/
```

## Default Admin Credentials

The SQL file includes a default admin user for local testing:

- Email: `admin@gmail.com`
- Password: `admin@gmail.com`

> Change this password after the first login for security.

## Admin Access

After logging in as the admin, you can access the admin dashboard and manage content from the admin pages.

## Notes

- This project is designed for local development and learning purposes.
- The application uses basic PHP session handling and mysqli queries.
- For production use, consider strengthening security, adding validation, and using prepared statements.

## License

This project is provided for educational/demo use.
