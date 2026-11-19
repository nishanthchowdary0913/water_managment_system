# Village Water Management System

PHP/SQLite application for managing village water facilities, supply availability, and resident complaints.

## Requirements

- PHP 8.1 or newer
- PHP PDO SQLite extension
- Apache or an equivalent PHP web server

## Configuration

Set `DB_PATH` to the writable SQLite file location.

- Default local value: `data/water_management.sqlite3`
- Free Render value: `/tmp/water_management.sqlite3` (ephemeral)

## Deploy

### Render

1. Push this folder to a GitHub repository.
2. In Render, choose **New > Blueprint** and select the repository.
3. Optionally set `ADMIN_USERNAME` and `ADMIN_PASSWORD` before the first deployment.
4. Deploy the `village-water-management` service.
5. Set `ADMIN_USERNAME` and `ADMIN_PASSWORD` before the first deployment to create the initial admin account.

The included `Dockerfile` and `render.yaml` provide the PHP Apache runtime.
