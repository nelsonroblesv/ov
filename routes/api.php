<?php 
use Illuminate\Support\Facades\Route;

Route::get('/ubicaciones-todas', function () {
    return \App\Models\UbicacionUsuario::with('user')
        ->orderBy('created_at')
        ->get()
        ->groupBy('user_id')
        ->map(function ($group) {
            $user = $group->first()->user;
            return [
                'user_name' => $user->name,
                'icon' => $user->icon_url ?? asset('icons/rocket.png'),
                'recorrido' => $group->map(fn($p) => [
                    'latitud' => $p->latitud,
                    'longitud' => $p->longitud,
                ]),
            ];
        })->values();
});
