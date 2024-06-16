# Refactoring

## Installation
```bash
    git clone https://github.com/sergeycherepanov/refactoring.git
    cd refactoring
    composer install
```
## Configuration
```bash
    cp .env.dist .env
```
> The dist file contains my mock server urls, you can use it as is or change

## Run
legacy implementation
```bash
    php bin/console calculate-legacy var/data/input.txt
```
new implementation
```bash
    php bin/console calculate var/data/input.txt
```

## PhpUnit
```bash
    php ./vendor/bin/phpunit tests
```

## PhpStan
```bash
    php vendor/bin/phpstan analyse --level 6 src tests
```
