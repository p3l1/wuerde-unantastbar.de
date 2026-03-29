# ABOUTME: Development task runner for the local WordPress environment.
# ABOUTME: Uses docker compose for all container lifecycle management.

.PHONY: up down restart logs shell wp env install

# Copy .env.example to .env if it doesn't exist, then start containers
up: env
	docker compose up -d
	@echo "WordPress running at http://localhost:8080"

# Stop and remove containers (data volume is preserved)
down:
	docker compose down

# Restart all containers
restart:
	docker compose restart

# Tail logs from all containers (Ctrl+C to stop)
logs:
	docker compose logs -f

# Open a shell in the WordPress container
shell:
	docker compose exec wordpress bash

# Run a WP-CLI command: make wp CMD="plugin list"
wp:
	docker compose run --rm --no-deps wpcli wp $(CMD)

# Bootstrap WordPress: run core install and activate the theme.
# Safe to re-run — wp core install is a no-op if already installed.
install:
	@echo "Waiting for WordPress to be ready..."
	@until docker compose run --rm --no-deps wpcli wp db check --ssl=false --quiet 2>/dev/null; do sleep 2; done
	@set -a; . ./.env; set +a; \
	docker compose run --rm --no-deps wpcli wp core install \
		--url="$$WP_URL" \
		--title="$$WP_TITLE" \
		--admin_user="$$WP_ADMIN_USER" \
		--admin_password="$$WP_ADMIN_PASSWORD" \
		--admin_email="$$WP_ADMIN_EMAIL" \
		--skip-email
	docker compose run --rm --no-deps wpcli wp theme activate wuerde-unantastbar

# Ensure .env exists
env:
	@test -f .env || (cp .env.example .env && echo "Created .env from .env.example — edit credentials if needed")
