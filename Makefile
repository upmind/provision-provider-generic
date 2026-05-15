COMPOSE ?= docker compose
PHP_VERSION ?= 8.1
PHP_VERSION_ORIGIN := $(origin PHP_VERSION)
SERVICE := php$(subst .,,$(PHP_VERSION))

.PHONY: help build build74 build81 up up74 up81 stop stop74 stop81 down restart ps logs shell bash php

help:
	@echo "Usage:"
	@echo "  make up PHP_VERSION=7.4|8.1    Start selected PHP container"
	@echo "  make up74                      Start PHP 7.4 container"
	@echo "  make up81                      Start PHP 8.1 container"
	@echo "  make stop PHP_VERSION=7.4|8.1  Stop selected PHP container"
	@echo "  make down                      Stop and remove all containers"
	@echo "  make shell                     Open shell in the only running PHP container"
	@echo "  make shell PHP_VERSION=7.4|8.1 Open shell in selected PHP container"
	@echo "  make logs PHP_VERSION=7.4|8.1  Follow logs of selected container"
	@echo "  make build PHP_VERSION=7.4|8.1  Build selected PHP image"
	@echo "  make build74                   Build PHP 7.4 image"
	@echo "  make build81                   Build PHP 8.1 image"
	@echo "  make ps                        Show running containers"

build:
	@$(MAKE) validate-version
	$(COMPOSE) build $(SERVICE)

build74:
	@$(MAKE) build PHP_VERSION=7.4

build81:
	@$(MAKE) build PHP_VERSION=8.1

up:
	@$(MAKE) validate-version
	$(COMPOSE) up -d $(SERVICE)

up74:
	@$(MAKE) up PHP_VERSION=7.4

up81:
	@$(MAKE) up PHP_VERSION=8.1

stop:
	@$(MAKE) validate-version
	$(COMPOSE) stop $(SERVICE)

stop74:
	@$(MAKE) stop PHP_VERSION=7.4

stop81:
	@$(MAKE) stop PHP_VERSION=8.1

down:
	$(COMPOSE) down

restart:
	@$(MAKE) validate-version
	$(COMPOSE) restart $(SERVICE)

ps:
	$(COMPOSE) ps

logs:
	@$(MAKE) validate-version
	$(COMPOSE) logs -f $(SERVICE)

shell:
	@running_services="$$($(COMPOSE) ps --services --status running | grep -E '^php(74|81)$$' || true)"; \
	if [ "$(PHP_VERSION_ORIGIN)" = "command line" ] || [ "$(PHP_VERSION_ORIGIN)" = "environment" ]; then \
		$(MAKE) -s validate-version; \
		target_service="$(SERVICE)"; \
	else \
		running_count="$$(printf "%s\n" "$$running_services" | sed '/^$$/d' | wc -l | tr -d ' ')"; \
		if [ "$$running_count" = "1" ]; then \
			target_service="$$running_services"; \
		elif [ "$$running_count" = "0" ]; then \
			echo "Error: no PHP container is running. Start one with 'make up74' or 'make up81'."; \
			exit 1; \
		else \
			echo "Error: multiple PHP containers are running. Use 'make shell PHP_VERSION=7.4' or 'make shell PHP_VERSION=8.1'."; \
			exit 1; \
		fi; \
	fi; \
	$(COMPOSE) exec "$$target_service" bash

bash: shell

php:
	@$(MAKE) validate-version
	$(COMPOSE) exec $(SERVICE) php -v

validate-version:
	@if [ "$(PHP_VERSION)" != "7.4" ] && [ "$(PHP_VERSION)" != "8.1" ]; then \
		echo "Error: PHP_VERSION must be 7.4 or 8.1 (got '$(PHP_VERSION)')"; \
		exit 1; \
	fi
