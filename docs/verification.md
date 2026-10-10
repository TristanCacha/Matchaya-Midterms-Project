# Local verification record

Run date: 10 October 2026. Environment: XAMPP PHP 8.2.12, MariaDB/MySQL, CodeIgniter 4.7.4, localhost on port 8084. The local database was seeded with illustrative records. Hosted checks are recorded separately after deployment.

| Check | Result | Evidence |
|---|---|---|
| PHP syntax | Pass | `php -l` over 105 application, API, and public PHP files: 0 failures. |
| Routes and filters | Pass | `spark routes` lists every management GET/POST route in the auth group, with global CSRF and secure headers. |
| Migrations and seed | Pass | Six migrations and `MatchaSeeder` completed on a fresh `matchaya_pos` database. |
| Anonymous management access | Pass | `/pos`, `/dashboard`, `/products`, `/customers`, `/staff`, `/sales`, and `/account` redirected to login. |
| Valid sale | Pass | A two-unit sale decreased stock by exactly two and wrote one row with quantity 2, total ₱380.00, and the signed-in staff ID. |
| Oversell | Pass | Quantity 99 was rejected with available-stock detail; stock and row count were unchanged. |
| Multi-item failure | Pass | A cart containing an out-of-stock item wrote no rows and did not decrement the other item. |
| Image rejection | Pass | PHP bytes renamed `.jpg` and a file above 2 MB were rejected. |
| Image preparation | Pass | A 900×700 JPEG product image was saved as a generated 640×640 JPEG and served through `/media`. A staff avatar was saved as 256×256; its test upload was then removed. |
| Deletion guards | Pass | Products, customers, and staff with sales were retained. Self-deletion was refused. |
| Last account | Not run separately | With one signed-in account, the self-deletion guard intercepts its deletion before the last-account branch. The branch exists in `Staff::delete`. |
| Blank password edit | Pass | Editing a staff record with an empty password preserved its existing hash. |
| Empty `product_meta` | Pass | The POS, dashboard, and customer profile rendered after temporarily clearing `product_meta`; rows were restored. |
| Seeded dashboard | Pass | Dashboard returned revenue, order volume, hourly chart, top sellers, and stock runway without errors. |
| Cloud upload and hosted deploy | Not run | No Cloudinary credentials, hosted database, GitHub repository, or Vercel deployment were supplied. The cloud path includes an incoming transformation fallback for hosts without GD but needs a live check. |
| Local regression run after image integration | Pass | `.tools/verify_matchaya.py` returned 21/21 checks, including auth, valid sale, rollback, guarded deletions, password preservation, upload rejection, 640×640 preparation, and private media route. The temporary test image was reset to the committed Usucha depiction. |
| Generated image manifest | Pass | `tools/check_demo_images.py` checked 17 product and 2 brand images, WebP encoding, recorded dimensions and byte sizes, square tile crops, and a <80 KiB tile budget. Public image total: 2,102,656 bytes. |
| Product-specific imagery | Pass | Contact sheet visually inspected against the menu; the two-tier images are mapped by `MatchaSeeder` and displayed with descriptive alt text. |
| InfinityFree deployment | Pass | `https://matchaya-it0049.page.gd/` served the app over HTTPS with a separate hosted database. See `docs/deployment-record.md`. |
| Hosted stock separation | Pass | A live ₱205 Hot Matcha Latte sale changed hosted stock 34→33; the local XAMPP database still held 34. |
| Hosted image upload | Pass | A valid WebP saved through product edit and loaded as a private-route 640×640 JPEG; the seeded static mapping was then restored. |
| Hosted mobile repair | Pass | The 360 px product list initially overlapped because the desktop 56 px row height overrode card height. A mobile height override and stylesheet cache version were deployed and visually checked. |

Browser inspection covered the POS and dashboard at 360, 768, and 1280 CSS pixels. The document width did not exceed the viewport at those sizes. Hosted widths were 345, 753, and 1265 px, respectively. Keyboard checks confirmed `/` focuses search, Tab reaches the first menu tile, and Enter adds it. All application controls inspected on the main management pages were at least 44 px high after the touch-target pass; POS tiles are at least 220 px high and Charge is 56 px. The local debug toolbar is a development-only framework control and is outside that measurement.

Contrast calculations use the WCAG relative-luminance formula. Text pairs: ink/foam 13.70:1, ink/mist 12.18:1, ink/paper 15.09:1, ink/fresh 6.29:1, shade/foam 7.33:1, shade/paper 8.07:1, glaze/foam 5.45:1, glaze/mist 4.84:1, glaze/paper 6.00:1, adzuki/foam 6.80:1, adzuki/paper 7.49:1. Control boundaries use glaze/paper 6.00:1, glaze/mist 4.84:1, or shade/paper 8.07:1. Fresh/foam is 2.18:1 and fresh/paper 2.40:1, so fresh is used as a fill with dark text and a dark border for actionable controls, not as text or a thin functional boundary.
