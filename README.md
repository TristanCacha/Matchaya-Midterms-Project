# Matchaya Point of Sale

Matchaya is a CodeIgniter 4 point-of-sale application for a sample matcha café and the IT0049 Web System Technologies midterm project. Staff can manage products, customers, and accounts, record sales without overselling, and review sales history. The menu and sales in the seeder are illustrative sample data, not actual café records.

The application uses PHP 8.2 or newer, CodeIgniter 4.7, MySQL or MariaDB, server-rendered HTML, CSS, and small vanilla JavaScript. Its timezone is `Asia/Manila`; money is displayed in Philippine pesos.

## Features and Grading Evidence

| Rubric Area | Implementation |
| :--- | :--- |
| **Architecture and design** | Explicit `app/Config/Routes.php`, one Model per table, migrations and seeders, `SaleService` for stock-safe transactions, responsive counter layout, 17 product-specific generated images with a manifest. |
| **Feature completeness** | Product, customer, and staff CRUD; single-item sale form; multi-item POS cart; sales history and receipts. |
| **Validation and security** | Validated forms and images, password hashes, CSRF, auth filter, bounded uploads outside `public/`, guarded deletion, transaction rollback. |
| **Code quality** | Controllers prepare responses; Models and services handle queries and business rules; views escape displayed values. |
| **Documentation and deployment** | This README, `docs/design.md`, `docs/technical.md`, image manifest, Word guide, migrations, SQL export, and an InfinityFree deployment path. |

## Local Setup with XAMPP on Windows

1.  Start MySQL in XAMPP. Create an empty MySQL database named `matchaya_pos` with `utf8mb4` encoding, or choose another name and set it in `.env`.
2.  Copy `.env.example` to `.env`. Check the database host, user, password, port, and `app.baseURL`. **Do not commit `.env`.**
3.  Install dependencies with Composer: `composer install`. XAMPP PHP must have `mysqli`, `intl`, `gd`, `mbstring`, `fileinfo`, and `openssl` available.
4.  In this directory, run migrations:
    ```cmd
    C:\xampp\php\php.exe -d extension=php_intl.dll -d extension=php_gd.dll spark migrate --all
    ```
5.  To load sample menu, customers, two staff accounts, and 14 days of illustrative sales, run the seeder:
    ```cmd
    C:\xampp\php\php.exe -d extension=php_intl.dll -d extension=php_gd.dll spark db:seed MatchaSeeder
    ```
    > ⚠️ **Important:** The seeder prints random temporary passwords for `counter1` and `counter2` **once**. Save them when the command runs. No fixed plaintext password or known hash is committed.
6.  Double-click `Start-Matchaya.cmd` or run:
    ```cmd
    C:\xampp\php\php.exe -d extension=php_intl.dll -d extension=php_gd.dll -S 127.0.0.1:8084 -t public vendor\codeigniter4\framework\system\rewrite.php
    ```
7.  Open [http://localhost:8084/](http://localhost:8084/). Without seed accounts, the first visit goes to `/setup` to create the first account. With seed accounts, sign in with a generated password.

> **Note:** Use either migrations or `database/matcha_pos.sql` for a fresh schema. The SQL export creates the same six tables but does not insert sample records or CodeIgniter's migration tracking row. Prefer migrations for an environment that will receive future updates. The seeder skips tables that already contain data, so repeat runs do not duplicate the sample set.

## Pages and Routes

| Page | Route | Purpose |
| :--- | :--- | :--- |
| First account | `GET/POST /setup` | Creates the first staff account only when no account exists. |
| Sign in | `GET/POST /login` | Verifies the stored password hash. |
| Counter | `/` or `/pos` | Category menu, cart, and charge. |
| Dashboard | `/dashboard` | Revenue, order volume, hourly shape, top sellers, customer and stock indicators. |
| Products | `/products` | Menu CRUD and restock. |
| Customers | `/customers` | Customer CRUD and derived stamp display. |
| Staff | `/staff` | Staff CRUD and avatar management. |
| My
