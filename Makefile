# Makefile for Smspilot SMS Gateway SaaS

.PHONY: build up down logs install test clean

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

logs:
	docker-compose logs -f

install:
	@echo "Visit http://localhost:8080/Install/install in your browser to complete installation."

clean:
	docker-compose down -v --remove-orphans
	rm -rf vendor node_modules


# Deploy to Render using render.yaml
.PHONY: deploy-render
deploy-render:
	render blueprint deploy

# Deploy static dashboard to Netlify (assumes Netlify CLI installed)
.PHONY: deploy-static
deploy-static:
	cd templates/dashboard && netlify deploy --prod

# Add more targets as needed, e.g., test, lint, etc.
