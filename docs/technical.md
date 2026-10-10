# Matchaya setup and technical guide

This guide explains how Matchaya's database, request flow, imagery, and sale transaction fit together. It is for the IT0049 reviewer or a developer running the project locally or on InfinityFree. Setup commands are in the README; host steps are in `docs/infinityfree.md`.

## Request flow

A browser request reaches `app/Config/Routes.php`. Protected routes first pass `AuthFilter`, which checks that the session's `userId` still exists. A controller validates submitted values, asks a Model or service for data or a write, and passes display data to a view. Views render escaped values and contain no database calls.

For a sale, `Pos::charge` or `Sales::create` validates the request shape and passes selected product IDs and quantities to `SaleService`. The service starts one transaction, locks each product, checks stock, applies a conditional decrement, computes the price from the database, writes sale rows, and commits. Any invalid line rolls back the whole order. The receipt view groups rows sharing timestamp, staff, and customer.

## Schema

`products` holds name, price, stock, optional image filename or URL, and creation time. `customers` holds contact details. `users` holds staff identities, the password hash in the assignment's `password` column, optional avatar, and creation time. `sales` holds one product per row, optional customer, staff member, quantity, computed total, and time. `product_meta` and `ci_sessions` are additive support tables.

Migrations are in `app/Database/Migrations` and can be reverted through CodeIgniter. `database/matcha_pos.sql` is a plain schema export. `MatchaSeeder` provides illustrative menu, customer, staff, and sale rows. It prints temporary passwords at runtime; it does not commit them.

## Image pipeline

The image manifest in `database/seeds/image-manifest.json` records the generated source, use basis, alt text, output paths, dimensions, and byte sizes for each of 17 product depictions and two brand still lifes. `tools/build_demo_images.py` produces 640 and 1280 px WebPs from the source files; `tools/check_demo_images.py` checks the committed outputs. `MatchaSeeder` connects products to static WebPs in `public/assets/demo/products`. Dynamic product and avatar uploads use `MediaStorage` and the restricted `/media` route. See `docs/IMAGE-CREDITS.md` for the full per-image record and `docs/image-contact-sheet.jpg` for visual review.

## Hosted separation

InfinityFree gets its own MySQL database and private `.env`. The root `.htaccess` routes requests to `public/` because InfinityFree fixes the document root at `htdocs`. This allows the project and bundled `vendor/` to be uploaded together while keeping source directories off the public route. The local XAMPP database remains independent. Hosted configuration uses InfinityFree's assigned MySQL hostname, `CI_ENVIRONMENT=production`, the HTTPS site URL, and `MEDIA_DRIVER=local` for persistent file uploads.

## Troubleshooting

| Symptom | Check |
|---|---|
| Database connection refused | Start MySQL; confirm `.env` host and port. |
| Unknown database | Create `matchaya_pos` or set the chosen database name in `.env`. |
| A route fails to load | Run `php spark routes`; confirm the controller and view paths. |
| Image upload fails | Locally check GD, fileinfo, the 2 MB limit, and write access to `writable/uploads`. On a host without GD, use the cloud driver and confirm the incoming Cloudinary transformation. |
| Image disappears after deploy | Use `MEDIA_DRIVER=cloud` on ephemeral hosting and configure Cloudinary. |
| Sign-in fails after seed | Use the generated password printed by that seed run; it is not stored as plaintext. |
| No category tabs | Populate `product_meta`; the core POS remains usable without it. |
| Stock did not change | Confirm the sale succeeded. Failed or oversold carts roll back without saving rows. |
| Receipt combines nearby orders | The required schema has no order ID. Orders with identical timestamp, staff, and customer can group together. |

## Known boundaries

This is an academic POS implementation. It does not record payment methods, discounts, VAT, refunds, tips, ingredient cost, or loyalty redemption. Add these only with an approved schema extension and a matching audit workflow for a real shop.
