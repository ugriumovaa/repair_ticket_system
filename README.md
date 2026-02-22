## Repair Ticket System

A minimal repair service web application built with Laravel 12, Inertia.js (Vue 3) and Spatie Permission.

The system implements two roles:

- **Dispatcher**

- **Technician**

It supports ticket creation, assignment, status transitions, filtering, and safe concurrent processing.

### Tech Stack

- PHP 8.3

- Laravel 12

- Inertia.js + Vue 3

- Spatie Laravel Permission

-  MySQL

- Docker


Running the Project
Option A — Docker (Recommended)
docker compose up --build

Application will be available at:

http://localhost:8000

Then run migrations and seed:

docker compose exec app php artisan migrate --seed
Option B — Without Docker

Install dependencies:

composer install
npm install

Copy environment:

cp .env.example .env
php artisan key:generate

Run migrations and seed:

php artisan migrate --seed

Build frontend:

npm run build

Start server:

php artisan serve
Seeded Users

The database seeder creates:

Dispatcher
email: dispatcher@example.com
password: password
Technicians
email: technician1@example.com
password: password

email: technician2@example.com
password: password
