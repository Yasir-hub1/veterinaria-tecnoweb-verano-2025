<?php

namespace App\Providers;

use App\Models\Visitas;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $currentPage = request()->path();
            $visit = Visitas::where('pagina', $currentPage)->first();
            $visitCount = $visit ? $visit->conteo : 0;

            $view->with('visitCount', $visitCount);
        });
    }
}
