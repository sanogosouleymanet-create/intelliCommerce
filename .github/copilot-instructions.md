# Copilot / AI Agent Instructions — intelliCommerce

Purpose: Help AI coding agents become productive quickly in this Laravel application.

- **Big picture**: This is a Laravel 10 web app (PHP ^8.1) served from a WAMP environment (project root). Backend is classic MVC: routes are declared in `routes/web.php`, controllers live in `app/Http/Controllers`, Eloquent models are in `app/Models`, and views are Blade files in `resources/views`.

- **Key files & entry points**:
  - `routes/web.php` — primary route definitions and some inline form handlers (see Vendeur creation route).
  - `app/Http/Controllers/` — controllers implementing business logic (e.g., `ProduitController`, `CommandeController`, `VendeurController`).
  - `app/Models/` — Eloquent models (notably `Vendeur.php`, `Produit.php`) with non-standard column names.
  - `resources/views/` — blade templates used by the routes (examples: `formulaireVendeur.blade.php`, `welcome.blade.php`).
  - `composer.json` / `package.json` — backend and frontend dependencies and scripts.

- **Project-specific conventions (important)**:
  - Database columns and pivot tables use PascalCase (e.g., `Nom`, `Prenom`, `MotDePasse`, `Vendeur_idVendeur`, `Produitcommande`) rather than Laravel's default snake_case. Always use the column names defined in models' `$fillable`.
  - Models sometimes declare custom table names or foreign keys (e.g., `Vendeur` sets `protected $table = 'vendeurs'`, `Produit` uses `Vendeur_idVendeur`). Do not assume default column names like `created_at` or `user_id` without checking the model/migration.
  - Password-like fields use `MotDePasse` — code hashes values with `Hash::make(...)` in routes/controllers; keep this convention when adding auth logic.

- **Data-flows & examples**:
  - Simple inline form handler in `routes/web.php` creates a `Vendeur` via `Vendeur::create([... 'MotDePasse' => Hash::make($request->MotDePasse) ...])`. When modifying or moving this logic into a controller, preserve the same column names and hashing.
  - `Produit` and `Commande` are connected through a pivot table named `Produitcommande` and use `withPivot('Quantite')` — follow that naming for queries and migrations.

- **Developer workflows / commands**:
  - Install PHP deps: `composer install`
  - Install JS deps / dev server: `npm install` then `npm run dev` (uses Vite). Build assets: `npm run build`.
  - Run the app locally (WAMP): either configure the WAMP vhost to point to `public/`, or run: `php artisan serve --host=127.0.0.1 --port=8000` (note: WAMP/Apache may already serve the project).
  - Database: edit `.env` for DB credentials, then `php artisan migrate` (this project contains only default Laravel migrations — other tables may pre-exist or be managed externally). Check `app/Models/*` to confirm expected columns.
  - Tests: run `php artisan test` or `vendor/bin/phpunit` (phpunit ^10 is in dev deps).

- **Dependencies & integrations**:
  - Backend: `laravel/framework` ^10, `laravel/sanctum`, `guzzlehttp/guzzle`.
  - Frontend: Vite + `laravel-vite-plugin` (see `package.json`).
  - Development tooling: `laravel/pint`, `phpunit`, `spatie/laravel-ignition` for nicer error pages in dev.

- **Common pitfalls for AI edits**:
  - DO NOT rename DB columns to snake_case automatically — many models rely on PascalCase column names (check `protected $fillable`).
  - When adding or changing migrations, confirm whether tables already exist in the database; several domain tables (produits, vendeurs, commandes) appear to be handled in models without corresponding custom migrations in `database/migrations/`.
  - When moving inline route logic into controllers, be explicit about the request fields (e.g., `$request->Nom`) and continue to use `Hash::make` for `MotDePasse`.
  - Maintain French naming used across views, routes, and fields (e.g., `formulaireVendeur`, `ConnexionVendeur`, `P1Client`) when adding new routes or views.

- **When in doubt**:
  - Inspect the model (`app/Models/*`) to learn the table name, `$fillable`, relationships, and pivot names before writing queries or migrations.
  - Search for usages of a column name across `routes/`, `app/`, and `resources/views/` to preserve consistency.

If anything above is unclear or you'd like examples (e.g., refactor the `formulaireVendeur` route into a controller action), tell me which area to expand and I'll update this file.
