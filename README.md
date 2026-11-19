# Village Water Management System

PHP/MySQL application for managing village water facilities, supply availability, and resident complaints.

## Requirements

- PHP 8.1 or newer
- PHP mysqli extension
- MySQL 8 or MariaDB 10.5+
- Apache or an equivalent PHP web server

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
3. Set `DB_HOST`, `DB_NAME`, `DB_USERNAME`, and `DB_PASSWORD` in the Render service environment.
4. Deploy the `village-water-management` service.

The included `Dockerfile` and `render.yaml` provide the PHP Apache runtime.
