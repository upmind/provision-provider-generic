# Development Environment

This project includes a Docker-based PHP development environment that can run either PHP 7.4 or PHP 8.1.

## Requirements

- Docker Desktop (or Docker Engine with Compose v2)
- GNU Make

## Available PHP Versions

- `7.4` (service: `php74`)
- `8.1` (service: `php81`)

## Start Containers

Use the version shortcuts:

```bash
make up74
# or
make up81
```

Or specify a version directly:

```bash
make up PHP_VERSION=7.4
make up PHP_VERSION=8.1
```

## Open a Bash Shell

```bash
make shell
```

`make shell` automatically attaches to the only running PHP container.

If both `php74` and `php81` are running, specify a version explicitly:

```bash
make shell PHP_VERSION=7.4
make shell PHP_VERSION=8.1
```

## Common Commands

```bash
make ps
make logs PHP_VERSION=8.1
make php PHP_VERSION=8.1
make stop PHP_VERSION=8.1
make down
```

## Help

To view all available Make targets:

```bash
make help
```

## Notes

- The repository root is mounted to `/workspace` in each container.
- If `PHP_VERSION` is not provided, `8.1` is used by default.
- Development images include `composer`, `git`, `unzip`, and the PHP `zip` extension.
- Rebuild after changing `Dockerfile` (for example, `make build74` or `make build81`).
