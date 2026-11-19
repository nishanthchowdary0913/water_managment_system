# Village Water Management System

PHP/MySQL application for managing village water facilities, supply availability, and resident complaints.

## Requirements

- PHP 8.1 or newer
- PHP mysqli extension
- MySQL 8 or MariaDB 10.5+
- Apache or an equivalent PHP web server

## Database setup

1. Create or select a MySQL database.
2. Import `database.sql` with phpMyAdmin or the MySQL CLI:

   ```sh
   mysql -u <username> -p < database.sql
   ```

3. Create the first admin account from PHP with a secure password:

   ```php
   <?php
   echo password_hash("REPLACE_WITH_A_STRONG_PASSWORD", PASSWORD_DEFAULT);
   ?>
   ```

4. Insert the generated hash:

   ```sql
   INSERT INTO admin (username, password)
   VALUES ('admin', '$2y$...generated_hash...');
   ```

## Configuration

Set these environment variables on the host:

- `DB_HOST` (default: `localhost`)
- `DB_PORT` (default: `3306`)
- `DB_NAME` (default: `water_management`)
- `DB_USERNAME` (default: `root`)
- `DB_PASSWORD` (default: empty)

Shared hosting that does not expose environment variables may edit `db_connect.php`; do not commit production credentials.

## Deploy

### Render

1. Push this folder to a GitHub repository.
2. In Render, choose **New > Blueprint** and select the repository.
3. Create or use an external MySQL-compatible database (Render does not provide managed MySQL).
4. Import `database.sql` into that database.
5. Set `DB_HOST`, `DB_NAME`, `DB_USERNAME`, and `DB_PASSWORD` in the Render service environment.
6. Deploy the `village-water-management` service.

The included `Dockerfile` and `render.yaml` provide the PHP Apache runtime.
