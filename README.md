# wuerde-unantastbar.de

WordPress theme for [wuerde-unantastbar.de](https://wuerde-unantastbar.de) — website of Verein für Menschenwürde und Demokratie e.V.

## Local development

### Requirements

- [Docker](https://www.docker.com/) with Compose

### First-time setup

```bash
make up       # start containers (creates .env from .env.example on first run)
make install  # bootstrap WordPress, activate theme
```

WordPress is then available at **http://localhost:8080**.
Admin login: see `WP_ADMIN_USER` / `WP_ADMIN_PASSWORD` in your `.env`.

### Hot reload

The `theme/` directory is bind-mounted into the container. Editing a file and
refreshing the browser is sufficient — no rebuild required.

### Common tasks

| Command | Description |
|---|---|
| `make up` | Start containers |
| `make down` | Stop containers (data preserved) |
| `make install` | Bootstrap WordPress + activate theme |
| `make logs` | Tail container logs |
| `make shell` | Shell into the WordPress container |
| `make wp CMD="..."` | Run any WP-CLI command |

**Example WP-CLI usage:**
```bash
make wp CMD="plugin list"
make wp CMD="post list"
```

### Resetting to a clean state

```bash
make down
docker volume rm wuerde-unantastbarde_mysql-data wuerde-unantastbarde_wordpress-data
make up && make install
```

## Project structure

```
theme/          WordPress theme (mounted into container for hot reload)
config/         Service configuration files
docker-compose.yml
Makefile
.env.example    Template for local credentials (copy to .env, never commit .env)
```
