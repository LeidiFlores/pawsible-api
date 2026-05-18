<div align="center">

<img src="https://readme-typing-svg.demolab.com?font=Nunito&weight=700&size=28&pause=1000&color=FF8FAB&center=true&vCenter=true&width=600&lines=%F0%9F%90%BE+pawsible-api;A+REST+API+that+actually+has+a+soul+✨" alt="Typing SVG" />

<br/>

**A production-ready Laravel REST API** — built for a pet shop & adoption platform,
but structured to be your personal starter kit for any serious backend project.

<br/>

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-005C84?style=flat-square&logo=mysql&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-Cache-DC382D?style=flat-square&logo=redis&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?style=flat-square&logo=docker&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-FF8FAB?style=flat-square)

</div>

---

## 🐶 What is this?

`pawsible-api` is a **fully-featured REST API** for a pet shop & adoption management system. It covers authentication, role-based access, pet catalog, adoption requests, and appointment scheduling — real domain logic, not just a to-do list.

> Built as a public portfolio project to demonstrate clean Laravel architecture, API design patterns, and production-ready practices. Fork it. Break it. Make it yours.

---

## ✨ Features

| Area | What's included |
| ---- | --------------- |
| 🔐 **Auth** | Register, login, logout via Laravel Sanctum (token-based) |
| 👥 **Roles & Permissions** | `admin`, `staff`, `adopter` — via Spatie Laravel Permission |
| 🐾 **Pet Catalog** | CRUD for pets with species, breed, age, status (`available`, `adopted`, `foster`) |
| 📋 **Adoption Requests** | Full request lifecycle: submit → review → approve/reject |
| 📅 **Appointments** | Schedule visits, manage slots, link to pets and users |
| 🔍 **Filtering & Search** | Query by species, status, location using Spatie Query Builder |
| 📄 **Pagination** | JSON:API-style cursor & offset pagination |
| ✅ **Form Requests** | All input validated via dedicated Request classes |
| 🧪 **Tests** | Feature tests with PHPUnit — auth flows, CRUD, permissions |
| 🚦 **Rate Limiting** | Per-user and per-route throttling |
| 📦 **API Resources** | Clean response transformation via Laravel Resources |

---

## 🏗️ Architecture

```
app/
├── Http/
│   ├── Controllers/Api/V1/   # Versioned controllers
│   ├── Requests/             # Form Request validation
│   ├── Resources/            # API Resource transformers
│   └── Middleware/           # Custom middleware (role checks, etc.)
├── Models/                   # Eloquent models with relationships
├── Services/                 # Business logic layer (AdoptionService, etc.)
├── Policies/                 # Authorization policies per model
└── Enums/                    # PHP 8.1+ enums (PetStatus, RequestStatus)

routes/
└── api.php                   # Versioned routes (v1)

database/
├── migrations/
├── factories/
└── seeders/                  # RolesSeeder, PetsSeeder, UsersSeeder
```

> Service layer separates business logic from controllers. Controllers are thin. Policies handle authorization. Enums replace magic strings.

---

## 🚀 Getting started

### Requirements

- PHP 8.3+
- Composer
- MySQL 8.0+ or MariaDB
- Redis (for cache & queues)
- Docker (optional but recommended)

### With Docker

```bash
git clone https://github.com/LeidiFlores/pawsible-api.git
cd pawsible-api
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### Without Docker

```bash
git clone https://github.com/LeidiFlores/pawsible-api.git
cd pawsible-api
cp .env.example .env
composer install
php artisan key:generate
# configure your .env DB and Redis values, then:
php artisan migrate --seed
php artisan serve
```

API available at `http://localhost:8000/api/v1`

---

## 📡 API Reference

### Auth

| Method | Endpoint | Auth | Description |
| ------ | -------- | ---- | ----------- |
| POST | `/api/v1/register` | ❌ | Register new user |
| POST | `/api/v1/login` | ❌ | Get Sanctum token |
| POST | `/api/v1/logout` | ✅ | Revoke token |

### Pets

| Method | Endpoint | Role | Description |
| ------ | -------- | ---- | ----------- |
| GET | `/api/v1/pets` | public | List all available pets |
| GET | `/api/v1/pets/{id}` | public | Get pet details |
| POST | `/api/v1/pets` | admin/staff | Create pet record |
| PUT | `/api/v1/pets/{id}` | admin/staff | Update pet |
| DELETE | `/api/v1/pets/{id}` | admin | Soft delete |

### Adoptions

| Method | Endpoint | Role | Description |
| ------ | -------- | ---- | ----------- |
| POST | `/api/v1/adoptions` | adopter | Submit adoption request |
| GET | `/api/v1/adoptions` | admin/staff | List all requests |
| PATCH | `/api/v1/adoptions/{id}/approve` | admin | Approve request |
| PATCH | `/api/v1/adoptions/{id}/reject` | admin | Reject with reason |

> Full Postman collection included in `/docs/pawsible-api.postman_collection.json`

---

## 🔒 Role matrix

| Action | admin | staff | adopter |
| ------ | :---: | :---: | :-----: |
| Manage pets | ✅ | ✅ | ❌ |
| View pet list | ✅ | ✅ | ✅ |
| Submit adoption request | ❌ | ❌ | ✅ |
| Review/approve adoptions | ✅ | ✅ | ❌ |
| Manage users | ✅ | ❌ | ❌ |
| Manage appointments | ✅ | ✅ | view own |

---

## 🧪 Running tests

This project uses **[Pest](https://pestphp.com/)** — Laravel's modern testing framework with expressive, readable syntax built on top of PHPUnit.

```bash
php artisan test
# or with coverage
php artisan test --coverage
```

Example test:

```php
it('allows an adopter to submit an adoption request', function () {
    $user = User::factory()->adopter()->create();
    $pet = Pet::factory()->available()->create();

    actingAs($user)
        ->postJson('/api/v1/adoptions', [
            'pet_id' => $pet->id,
            'message' => 'I would love to adopt!',
        ])
        ->assertCreated();
});
```

Tests cover: registration, login, token revocation, pet CRUD with role enforcement, adoption request lifecycle, and appointment creation.

---

## 🌱 Seeders

After running `--seed`, you'll have:

| Role | Email | Password |
| ---- | ----- | -------- |
| admin | admin@pawsible.dev | password |
| staff | staff@pawsible.dev | password |
| adopter | user@pawsible.dev | password |

Plus 20 seeded pets across different species and statuses.

---

## 🛠️ Key packages used

| Package | Purpose |
| ------- | ------- |
| `pestphp/pest` | Modern test framework (built on PHPUnit) |
| `laravel/sanctum` | Token-based API auth |
| `spatie/laravel-permission` | Roles & permissions |
| `spatie/laravel-query-builder` | Filtering, sorting, includes |
| `spatie/laravel-data` | Typed DTOs |
| `laravel/telescope` | Local debugging (dev only) |

---

## 💡 Design decisions

- **Versioned routes** (`/api/v1/`) from day one — easier to maintain breaking changes later
- **Service layer** over fat controllers — business logic lives in `app/Services/`
- **PHP Enums** for status fields — no magic strings, full IDE support
- **Soft deletes** on pets and users — data integrity over hard deletion
- **Policies over middleware** for authorization — granular, testable, Laravel-idiomatic

---

## 📬 Contact

Made with 🐾 by **[Leidi Flores](https://www.linkedin.com/in/leidi-flores-reyes/)** — Senior Full Stack Engineer

[![LinkedIn](https://img.shields.io/badge/LinkedIn-leidi--flores--reyes-0077B5?style=flat-square&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/leidi-flores-reyes/)
[![GitHub](https://img.shields.io/badge/GitHub-LeidiFlores-181717?style=flat-square&logo=github&logoColor=white)](https://github.com/LeidiFlores)

---

<div align="center">
<sub>🐾 every pet deserves a home · every codebase deserves good architecture</sub>
</div>
