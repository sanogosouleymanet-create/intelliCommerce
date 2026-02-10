<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MotInterdit;

class MotInterditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MotInterdit::insert([
            ['mot' => 'idiot', 'poids' => 2],
            ['mot' => 'imbecile', 'poids' => 2],
            ['mot' => 'arnaque', 'poids' => 3],
            ['mot' => 'escroc', 'poids' => 4],
            ['mot' => 'stupide', 'poids' => 1],
            ['mot' => 'nul', 'poids' => 1],
            ['mot' => 'incompétent', 'poids' => 2],
            ['mot' => 'horrible', 'poids' => 3],
            ['mot' => 'pire', 'poids' => 2],
            ['mot' => 'détestable', 'poids' => 3],
            ['mot' => 'honteux', 'poids' => 3],
            ['mot' => 'ridicule', 'poids' => 2],
            ['mot' => 'abominable', 'poids' => 4],
            ['mot' => 'affreux', 'poids' => 3],
            ['mot' => 'lamentable', 'poids' => 3],
            ['mot' => 'scandaleux', 'poids' => 4],
            ['mot' => 'terrible', 'poids' => 3],
            ['mot' => 'salopard', 'poids' => 4],
            ['mot' => 'connard', 'poids' => 4],
            ['mot' => 'salaud', 'poids' => 4],
            ['mot' => 'merde', 'poids' => 3],
                ['mot' => 'putain', 'poids' => 3],
                ['mot' => 'bordel', 'poids' => 2],
                ['mot' => 'foutre', 'poids' => 2],
                ['mot' => 'con', 'poids' => 3],
                ['mot' => 'pédé', 'poids' => 4],
                ['mot' => 'salope', 'poids' => 4],
                ['mot' => 'enculé', 'poids' => 4],
                ['mot' => 'ta gueule', 'poids' => 4],
                ['mot' => 'nique', 'poids' => 4],
                ['mot' => 'bite', 'poids' => 3],
                ['mot' => 'couille', 'poids' => 3],
                ['mot' => 'cul', 'poids' => 2],
                ['mot' => 'fils de pute', 'poids' => 4],
                ['mot' => 'enfoiré', 'poids' => 4],
                ['mot' => 'salaud de pauvre', 'poids' => 4],
        ]);
    }
}
