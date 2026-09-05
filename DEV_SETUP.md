Developer setup & verification (local XAMPP)

Steps I ran to consolidate DB and verify core functionality:

1. Check environment and required PHP extensions:

```bash
php tools/setup.php
```

2. Import SQL files (runs all `.sql` in `database/`):

```bash
php database/import_db.php
```

3. Audit schema against `database/eoffice.sql`:

```bash
php database/audit_schema.php
```

4. Seed demo accounts and template (if empty):

```bash
php tools/seed_users.php
php tools/seed_templates.php
```

5. Run the end-to-end functional test (creates surat, RSA key, signs, verifies):

```bash
php tools/e2e_test.php
```

Notes & next steps:
- If `tools/setup.php` reports missing `gd`, enable the GD extension in XAMPP's PHP (`php.ini`) for full image support.
- You can view generated PDFs & QR in `public/pdf` and `public/qr`.
- If you want, I can commit these changes to a new branch `feature/full-functional` and continue hardening controllers, adding tests, and integrating CI.
