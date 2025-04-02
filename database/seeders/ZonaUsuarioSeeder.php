<?php

namespace Database\Seeders;

use App\Models\Zonas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonaUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las zonas con user_id asignado
        $zonas = Zonas::whereNotNull('user_id')->get();

        foreach ($zonas as $zona) {
            DB::table('zona_usuario')->insert([
                'zonas_id' => $zona->id,
                'users_id' => $zona->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
