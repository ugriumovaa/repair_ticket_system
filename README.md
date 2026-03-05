## Repair Ticket System

A minimal repair service web application built with Laravel 12, Inertia.js (Vue 3).

The system implements two roles:

- **Dispatcher**

- **Technician**

It supports ticket creation, assignment, status transitions, filtering.

---

### Tech Stack

- PHP 8.3

- Laravel 12

- Inertia.js + Vue 3

- Spatie Laravel Permission

-  MySQL

- Docker

---

### Running the Project

 **Start Docker containers**
 
`docker compose up -d --build`

**Install dependencies**

`docker compose exec app composer install`

`docker compose exec app npm install`

`docker compose exec app npm run build`

**Copy environment:**

`cp .env.example .env`

**Run migrations and seed:**

`docker compose exec app php artisan migrate --seed`

---

### Test Users

After seeding, the following test users are available:

**Dispatcher**
- email: dispatcher@example.com
- password: password

**Technicians**
- email: technician1@example.com
- password: password


- email: technician2@example.com
- password: password
