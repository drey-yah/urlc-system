# Getting Started

Welcome to the **URLC System** Developer Handbook. This guide will walk you through setting up the application locally for development and testing.

## Prerequisites

Ensure you have the following installed on your machine:
- **PHP** (>= 8.1)
- **Composer** (PHP Package Manager)
- **Node.js** (>= 18.0) & **NPM**
- **PostgreSQL** or a **Supabase Account** (for database hosting)

---

## Local Setup Steps

### 1. Clone the Repository
Clone the project repository to your local machine:
```bash
git clone https://github.com/drey-yah/urlc-system.git
cd urlc-system
```

### 2. Install PHP Dependencies
Use Composer to install Laravel backend dependencies:
```bash
composer install
```

### 3. Install Frontend Dependencies
Install npm packages required for Tailwind CSS, Laravel Mix, and AlpineJS:
```bash
npm install
```

### 4. Configure Environment Variables
Copy the example environment file to create your local `.env`:
```bash
cp .env.example .env
```
Open `.env` and configure your database and third-party keys:
*   **Database:** Setup `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` (pointing to your Supabase PostgreSQL pooler or local DB).
*   **Storage (S3 compatible):** Configure `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, and `AWS_ENDPOINT` to connect to your Supabase Storage bucket.

### 5. Generate Application Key
Generate the Laravel encryption key:
```bash
php artisan key:generate
```

### 6. Run Database Migrations
Run the migrations to create the database tables and set up RLS:
```bash
php artisan migrate
```

---

## Running the Application

### Start the Laravel Local Server
```bash
php artisan serve
```
The application will be accessible at `http://localhost:8000`.

### Start Assets Watcher (Compiling CSS & JS)
To compile assets and listen for changes in Blade templates or Tailwind styles, run:
```bash
npm run dev
# or npm run watch
```
