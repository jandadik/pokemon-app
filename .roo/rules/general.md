---
description: 
globs: 
alwaysApply: true
---
# Routing Rule

1.  **Location:** All web routes for the browser (served via Inertia) are defined in files within the `routes/` directory and registered through the main **`routes/web.php`** file. API routes (if any) would be in `routes/api.php`. Console command routes are in `routes/console.php`.
2.  **File Organization:** Routes are logically divided into separate files within `routes/` based on the application's functional areas (e.g., `auth.php`, `admin.php`, `catalog.php`, `collections.php`, `profile.php`, `public.php`).
3.  **Loading:** These specific files are registered (loaded) in the main `routes/web.php` file using `Route::group(base_path('routes/filename.php'))`.
4.  **URL Prefixes:** If all routes within a given file share a common URL segment, the `prefix('segment')` method is used on the `Route::group()` in `routes/web.php` (e.g., `prefix('admin')`).
5.  **Route Naming:** **All** routes **must** have a name. Names are automatically prefixed according to the group name defined in `routes/web.php` using the `name('prefix.')` method (e.g., `name('admin.')`, `name('catalog.')`). Within the specific files (`admin.php`, `catalog.php`...), individual routes are then assigned a name using the `name('route_name')` method. The resulting name for the `route()` helper will be `prefix.route_name` (e.g., `route('admin.users.index')`). Use dot notation for structuring names.
6.  **Middleware:** Middleware specific to an entire functional area is applied to the `Route::group()` in `routes/web.php` using the `middleware(['middleware1', 'middleware2'])` method. Middleware for individual routes or smaller groups is defined directly within the specific files (`admin.php`, `catalog.php`...).
7.  **Controllers:** Routes should primarily point to methods in controllers (typically located in `app/Http/Controllers/` and further structured by area). Using closures (`function () { ... }`) directly in route definitions is only allowed for very simple cases. For web routes, controllers return a response using `Inertia::render('VueComponentName', $data)`.
8.  **Route Model Binding:** Utilize implicit and explicit Route Model Binding for automatic model loading based on URL parameters.
9.  **Resource Controllers:** For standard CRUD operations, prefer using `Route::resource('name', Controller::class)` for clarity and adherence to conventions. Limit the generated routes as needed using `only()` or `except()`.
10. **Cleanliness of `web.php`:** The `routes/web.php` file is solely for registering and configuring groups (loading files, prefixes, middleware, group names). Do not define specific application routes directly within it.

# Middleware Rule

We are using Laravel 12 and middleware (both global and route-specific aliases) are primarily defined and registered within the `bootstrap/app.php` file.



