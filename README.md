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
