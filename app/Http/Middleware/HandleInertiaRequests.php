<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'success' => $request->session()->get('success'),
                'new_login' => $request->session()->get('new_login')
            ],
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'roles' => $request->user()->getRoleNames(),
                'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                'can' => $request->user()->permissions->pluck('name'),
                'two_factor_enabled' => $request->user()->two_factor_enabled,
            ] : null,
            'locale' => App::getLocale(),
            'locales' => config('app.available_locales', ['cs', 'en']),
            'translations' => $this->getTranslationsForCurrentLocale(),
        ]);
    }

    /**
     * Načte překlady pro aktuální jazyk aplikace
     *
     * @return array
     */
    private function getTranslationsForCurrentLocale(): array
    {
        $locale = App::getLocale();
        $translations = [];

        // Načteme překlady z jednotlivých souborů
        foreach (['app', 'auth', 'ui', 'validation'] as $file) {
            if (file_exists(resource_path("lang/{$locale}/{$file}.php"))) {
                $translations[$file] = trans($file, [], $locale);
            }
        }

        return $translations;
    }
}
