# Live deployment record

Deployment date: 10 October 2026. URL: <https://matchaya-it0049.page.gd/>. InfinityFree hosting account: `if0_43139444`, labeled **Matchaya IT0049 POS**. Hosted MySQL database: `if0_43139444_matchaya_pos` on the hostname supplied by InfinityFree. Local XAMPP database: `matchaya_pos`. These are separate databases with separate `.env` files and separate POS passwords.

The hosted site received the root-level `matchaya-infinityfree-htdocs.zip` contents in `htdocs` and a private production `.env` uploaded separately. phpMyAdmin reported a successful import of 75 SQL queries; the hosted database contained seven tables and 17 products. The hosted code matched the project tree after the phone product-list repair and stylesheet cache version update.

| Live check | Result | Evidence |
|---|---|
| HTTPS and authentication | Pass | `/` redirected to `/login`; separate hosted `counter1` credentials opened `/pos`. |
| Static product images | Pass | Product photos loaded from `/assets/demo/products/`; the Usucha sample mapping was restored after upload testing. |
| Valid sale and receipt | Pass | One Hot Matcha Latte sale produced receipt `#136` for ₱205.00 and a success message. |
| Hosted stock decrement | Pass | Hot Matcha Latte changed from 34 to 33 in the hosted POS. The local XAMPP row remained 34. |
| Oversell refusal | Pass | Quantity 99 returned “Only 33 Hot Matcha Latte left. You asked for 99.” No sale was recorded. |
| Dashboard | Pass | Revenue, orders by hour, top sellers, inventory runway, and customer patterns rendered. |
| Image upload and preparation | Pass | A valid Usucha WebP uploaded through product edit, saved under `/media/products/{random}.jpg`, and loaded at 640×640. The database image field was restored to the static demo path. |
| Responsive phone product list | Pass after fix | A 360 px visual check exposed overlapping table cards. A mobile row-height override fixed it and was deployed with a stylesheet cache version update. |
| POS at 360, 768, and 1280 CSS px | Pass | Document widths were 345, 753, and 1265 px respectively, within each viewport. The phone order sheet, tablet grid, and desktop counter rendered. |
| Keyboard | Pass | `/` focused POS search and Enter on a focused product button added the item. The review cart was cleared afterward. |

The one live sale remains in the hosted illustrative data. A processed Usucha image from the upload check remains as an unreferenced file in `writable/uploads`; it is outside the public executable path and does not alter the seeded menu. Vercel, Cloudinary, and GitHub publishing were not exercised for this InfinityFree deployment.
