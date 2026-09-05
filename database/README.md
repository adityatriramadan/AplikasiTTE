Database import helper

Usage (from project root):

1. Ensure MySQL (XAMPP) is running.
2. Verify credentials in `config/database.php` (DB_HOST, DB_USER, DB_PASS).
3. Run:

```bash
php database/import_db.php
```

What it does:
- Executes all `.sql` files in `database/` in alphabetical order.
- The main schema is in `eoffice.sql`. Additional fixes/seeds are in other files.

Notes:
- The script uses `mysqli_multi_query` so it can run `CREATE DATABASE` and `USE` statements.
- If PHP's `exec` or `mysqli` extensions are disabled, import manually using the MySQL client or phpMyAdmin.
