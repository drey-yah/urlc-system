# Row Level Security (RLS) & API Security

To comply with high-security standards, all database tables exposed in the `public` schema have **Row Level Security (RLS)** enabled.

---

## The Security Model

Supabase automatically exposes all tables in the `public` schema via its HTTP Data API (using PostgREST). If RLS is disabled, anyone possessing the database’s public `anon` API key can read or write to tables directly by sending HTTP requests.

### RLS Policies
By enabling RLS on all tables without defining explicit public-facing policies, we configure a **default-deny** policy:
*   **PostgREST Requests:** Any client trying to query `https://<project-id>.supabase.co/rest/v1/<table_name>` using `anon` or `authenticated` keys will receive `401 Unauthorized` or empty results.
*   **Direct Database Port (Laravel):** The Laravel application connects to the database via standard PostgreSQL protocol on port `6543`. It authenticates as the `postgres` role, which bypasses RLS policies entirely.

---

## Verifying RLS in the Database

You can run the following SQL query in the Supabase SQL editor or via Artisan Tinker to verify the RLS status of all tables:

```sql
SELECT tablename, rowsecurity FROM pg_tables WHERE schemaname = 'public';
```

If `rowsecurity` returns `true` (or `1`) for all tables, RLS is active.

### How Laravel Bypasses RLS
PostgreSQL allows specific database roles to bypass RLS checks using the `BYPASS RLS` attribute. To verify that our database user has this privilege:

```sql
SELECT r.rolname, r.rolsuper, r.rolbypassrls FROM pg_roles r WHERE r.rolname = current_user;
```

*   `rolname`: `postgres`
*   `rolbypassrls`: `1` (True)

Since Laravel connects using this role, RLS does not intercept queries made by the web backend.

---

## Best Practices for Future Migrations

Whenever you create a new table via a Laravel migration, **always enable RLS** at the end of the migration's `up()` method:

```php
public function up()
{
    Schema::create('new_table', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    // Enable Row Level Security
    DB::statement('ALTER TABLE public.new_table ENABLE ROW LEVEL SECURITY;');
}

public function down()
{
    Schema::dropIfExists('new_table');
}
```
This ensures your tables remain secure from day one.
