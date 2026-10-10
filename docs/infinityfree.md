# InfinityFree deployment and database separation

This guide is for the IT0049 Matchaya sample, using InfinityFree's fixed `htdocs` web root. The repository's root `.htaccess` internally rewrites every request to `public/`; upload the *contents* of this project into `htdocs`, so `htdocs/.htaccess`, `htdocs/public/index.php`, `htdocs/app`, and `htdocs/vendor` are siblings.

## Hosted database

1. Create a **new** MySQL database in the InfinityFree account. The service supplies its database name, username, password, and MySQL hostname. Record them privately. The hostname is not `localhost`.
2. Import a fresh Matchaya SQL seed into that database with InfinityFree's phpMyAdmin. The local XAMPP `matchaya_pos` database stays on the user's computer and is never used by the hosted app.
3. The hosted account has its own staff password hash. Do not publish the plaintext staff password or the hosted database password in the repository or the submission ZIP.

## Files and configuration

1. Upload the project files, including `vendor/`, into `htdocs`. An archive may be extracted locally through InfinityFree's file manager before upload; its extraction action uploads the resulting files. Do not upload a nested `matcha-pos-it0049` folder as the site root.
2. Create a private `htdocs/.env` based on `.env.example`. Set `CI_ENVIRONMENT=production`, `app.baseURL='https://YOUR-DOMAIN/'`, `database.default.hostname`, `.database`, `.username`, `.password`, `.port=3306`, `.DBDriver=MySQLi`, and `MEDIA_DRIVER=local`.
3. Keep `htdocs/writable` writable. Static seeded product WebPs live in `public/assets` and do not depend on uploads. User uploads live in `writable/uploads` and are served through the validated `/media` route.

## Smoke check

Open the hosted HTTPS URL in a private browser window. Confirm login, dashboard, product imagery, and a valid sale. Confirm the sale decreases stock on the hosted database and appears in history. Check an oversell is refused without altering hosted stock. Then visit the localhost site and confirm its stock was untouched. Upload one valid image to check the hosted GD and writable permissions. Keep test sales clearly identified as illustrative sample data.

InfinityFree's official [CodeIgniter 4 deployment guide](https://forum.infinityfree.com/t/how-to-setup-a-codeigniter-4-project-on-infinityfree/40923) describes the root rewrite approach. Its [MySQL migration guide](https://forum.infinityfree.com/t/how-to-migrate-your-website-to-infinityfree/49315/1) describes using the dashboard's MySQL hostname and phpMyAdmin import. InfinityFree [does not allow external MySQL connections on free accounts](https://forum.infinityfree.com/t/connecting-to-mysql-from-an-external-application/49339), which further keeps this database distinct from local XAMPP.
