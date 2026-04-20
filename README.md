# Learning + Service Booking Platform

Laravel API + Vue/Vuetify + TypeScript implementation for the Full-Stack technical assessment.

## Stack

- Backend: Laravel 13, MySQL, JWT (`tymon/jwt-auth`)
- Authorization: `spatie/laravel-permission`
- Query/filtering/pagination: `spatie/laravel-query-builder`
- Frontend: Vue 3 + Inertia + Vuetify + TypeScript
- State/API: Pinia + Axios

## Architecture

The API uses a clean separation:

- `app/Repositories/*`: querying and data-access concerns
- `app/Services/*`: business logic and orchestration
- `app/Http/Controllers/Api/*`: thin request/response orchestration only
- `app/Http/Requests/*`: validation + authorization
- `app/Http/Resources/*`: API response formatting

## Core Features

- JWT register/login/logout/me endpoints
- Role support (`admin`, `user`) via Spatie Permission
- Course listing + admin-only course creation
- Enrollment endpoint with duplicate prevention
- Appointment booking + user-scoped listing
- Query Builder on list endpoints (`courses`, `appointments`) for filter/sort/pagination

## API Endpoints

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`
- `GET /api/courses`
- `POST /api/courses` (admin only)
- `POST /api/enroll`
- `POST /api/appointments`
- `GET /api/appointments`

## Frontend Pages

- `/app/login`
- `/app/register`
- `/app/courses`
- `/app/appointments`

## Setup

1. Install backend dependencies:
   - `composer install`
2. Install frontend dependencies:
   - `npm install`
3. Configure environment:
   - Copy `.env.example` to `.env`
   - Set MySQL credentials
4. Generate app key and JWT secret:
   - `php artisan key:generate`
   - `php artisan jwt:secret`
5. Run migrations + seed:
   - `php artisan migrate --seed`
6. Start app:
   - `composer run dev`

## Seeded Credentials

- Admin: `admin@example.com` / `password`
- User: `test@example.com` / `password`

## Postman and Demo

- Postman collection: `docs/Learning-Service-Booking.postman_collection.json`
- Loom walkthrough script: `docs/Loom-Demo-Script.md`

## Assumptions

- API-first JWT flow is the assessed path.
- Spatie Permission tables satisfy the roles requirement.
- MySQL is the primary runtime DB.
- Existing Fortify/session auth remains in project but is non-core for the assessment flow.
