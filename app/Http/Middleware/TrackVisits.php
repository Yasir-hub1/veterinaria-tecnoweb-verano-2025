<?php

namespace App\Http\Middleware;

use App\Models\Visitas;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $currentPage = $request->path(); // Get the current page path

        // Find the visit entry for the current page or create a new one
        $visit = Visitas::firstOrCreate(['pagina' => $currentPage]);
        $visit->conteo++;
        $visit->fecha = Carbon::now();
        $visit->save();

        return $next($request);
    }
}
