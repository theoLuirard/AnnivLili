<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NumericQuiz;
use App\Models\User;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if ($admin) {
            NumericQuiz::create([
                'question' => 'Combien de pays sont membres de l\'ONU ?',
                'correct_answer' => 193,
                'description' => 'Pensez aux états indépendants reconnus par l\'ONU',
                'status' => 'draft',
                'created_by' => $admin->id,
            ]);

            NumericQuiz::create([
                'question' => 'Quel est le plus haut sommet du monde en mètres ?',
                'correct_answer' => 8849,
                'description' => 'C\'est en Asie du Sud',
                'status' => 'draft',
                'created_by' => $admin->id,
            ]);

            NumericQuiz::create([
                'question' => 'En quelle année l\'homme a marché sur la lune ?',
                'correct_answer' => 1969,
                'description' => 'Apollo 11',
                'status' => 'draft',
                'created_by' => $admin->id,
            ]);
        }
    }
}
