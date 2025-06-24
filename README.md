# Setup Guide for PHP 8.4 + Symfony 7.3 + SQLite on WSL2

This guide covers setting up PHP 8.4, Symfony 7.3, SQLite on WSL2 (Ubuntu),
managing the project with GitHub, and running tests with GitHub Actions.

## Environment

- Windows 11 + WSL2 (Ubuntu)
- PHP 8.4
- Symfony 7.3
- SQLite

---

## 1. Install PHP 8.4

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.4-cli php8.4-sqlite3 php8.4-curl php8.4-xml php8.4-mbstring php8.4-zip php8.4-intl php8.4-opcache
```

Verify PHP installation:

```bash
php -v
```

---

## 2. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Verify Composer installation:

```bash
composer --version
```

---

## 3. Install Symfony CLI

```bash
curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
sudo apt install symfony-cli
```

Verify Symfony CLI installation:

```bash
symfony -v
```

---

## 4. Create Symfony Project

```bash
symfony new my_project --version="7.3.*" --webapp
cd my_project
```

---

## 5. Configure SQLite

Edit `.env`:

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

Create the database:

```bash
php bin/console doctrine:database:create
```

---

## 6. Run the Server

```bash
symfony server:start
```

Open `http://localhost:8000` in your browser to verify.

---

## 7. Set up GitHub Repository

Edit `.env.dev` to set a non-sensitive `APP_SECRET`:

```env
APP_SECRET="THIS_IS_FOR_DEV_SO_NOT_REALLY_SECRET"
```

Create a repository on GitHub and push your project:

```bash
git init
git branch -M main
git add .
git commit -m "Initial commit"
git remote add origin git@github.com:<username>/<repo-name>.git
git push -u origin main
```

---

## 8. Configure GitHub Actions for Testing

Install PHP-CS-Fixer:

```bash
composer require --dev friendsofphp/php-cs-fixer
```

Create `.github/workflows/ci.yml` in your project root:

```yaml
name: CI

on: [push, pull_request]

jobs:
    symfony-tests:
        runs-on: ubuntu-latest

        steps:
            - uses: actions/checkout@v4

            - name: Set up PHP 8.4
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.4"
                  extensions: sqlite, intl, mbstring, xml, zip
                  coverage: none

            - name: Install dependencies
              run: composer install --prefer-dist --no-progress

            - name: Check coding standards (optional)
              run: PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --dry-run --diff

            - name: Run tests (PHPUnit)
              run: vendor/bin/phpunit
```

Currently, PHP-CS-Fixer does not officially support PHP 8.4, thus
`PHP_CS_FIXER_IGNORE_ENV=1` is set to bypass version checks temporarily.

Commit and push your changes to trigger tests via GitHub Actions.

---

## 9. Run PHPUnit Tests

Symfony 7.3 includes PHPUnit 12 by default:

```bash
php bin/phpunit
```

Ensure all tests pass successfully.

---

## 10. Run PHPStan check

```bash
composer require --dev phpstan/phpstan
composer require --dev phpstan/phpstan-doctrine
composer require --dev phpstan/phpstan-symfony
vendor/bin/phpstan analyse src --level=max
```

if you encounter issues, you may need to fix them, ignore them on /phpstan.dist.neon config , or lower the level.

---

Your Symfony setup on WSL2 is now complete, including GitHub management and
automated testing with GitHub Actions.
