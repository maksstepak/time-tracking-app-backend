# Time tracking app backend

This is a time tracking app backend.

## 🔧 Technologies

- [Laravel 9](https://laravel.com/)
- [MariaDB](https://mariadb.org/)

## 🛠️ Setup

### Prerequisites

- Docker

### Installation

```bash
# Copy the .env.example file and make the required configuration changes in the .env file
cp .env.example .env

# Start the containers
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Seed the database
./vendor/bin/sail artisan db:seed

# Run tests
./vendor/bin/sail artisan test

# Fix the code formatting
./vendor/bin/sail bin pint

# Generate the documentation
./vendor/bin/sail artisan l5-swagger:generate 
```
