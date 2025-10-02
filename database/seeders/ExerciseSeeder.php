<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // CHEST
            [
                'name' => 'Bench Press',
                'aliases' => ['bench', 'bp', 'flat bench', 'barbell bench'],
                'category' => 'strength',
                'muscle_group' => 'chest',
                'equipment' => 'barbell',
                'difficulty' => 'intermediate',
                'description' => 'Lie on bench, lower bar to chest, press up',
                'tags' => ['compound', 'push', 'horizontal'],
            ],
            [
                'name' => 'Incline Bench Press',
                'aliases' => ['incline bench', 'incline bp', 'incline press'],
                'category' => 'strength',
                'muscle_group' => 'chest',
                'equipment' => 'barbell',
                'difficulty' => 'intermediate',
                'description' => 'Bench press at 30-45 degree angle',
                'tags' => ['compound', 'push', 'incline'],
            ],
            [
                'name' => 'Dumbbell Chest Press',
                'aliases' => ['db press', 'dumbbell press', 'db chest press'],
                'category' => 'strength',
                'muscle_group' => 'chest',
                'equipment' => 'dumbbell',
                'difficulty' => 'beginner',
                'description' => 'Press dumbbells from chest level',
                'tags' => ['compound', 'push'],
            ],
            [
                'name' => 'Push-ups',
                'aliases' => ['pushup', 'push up', 'pressup'],
                'category' => 'strength',
                'muscle_group' => 'chest',
                'equipment' => 'bodyweight',
                'difficulty' => 'beginner',
                'description' => 'Classic bodyweight chest exercise',
                'tags' => ['compound', 'push', 'bodyweight'],
            ],

            // BACK
            [
                'name' => 'Deadlift',
                'aliases' => ['dl', 'dead lift', 'conventional deadlift'],
                'category' => 'strength',
                'muscle_group' => 'back',
                'equipment' => 'barbell',
                'difficulty' => 'advanced',
                'description' => 'Lift bar from floor to standing position',
                'tags' => ['compound', 'pull', 'posterior'],
            ],
            [
                'name' => 'Pull-ups',
                'aliases' => ['pullup', 'pull up', 'chin up'],
                'category' => 'strength',
                'muscle_group' => 'back',
                'equipment' => 'bodyweight',
                'difficulty' => 'intermediate',
                'description' => 'Hang from bar, pull chin over bar',
                'tags' => ['compound', 'pull', 'bodyweight', 'vertical'],
            ],
            [
                'name' => 'Barbell Row',
                'aliases' => ['bb row', 'bent over row', 'barbell rows'],
                'category' => 'strength',
                'muscle_group' => 'back',
                'equipment' => 'barbell',
                'difficulty' => 'intermediate',
                'description' => 'Bent over, pull bar to abdomen',
                'tags' => ['compound', 'pull', 'horizontal'],
            ],
            [
                'name' => 'Lat Pulldown',
                'aliases' => ['lat pull down', 'pulldown', 'lat pull'],
                'category' => 'strength',
                'muscle_group' => 'back',
                'equipment' => 'machine',
                'difficulty' => 'beginner',
                'description' => 'Pull bar down to chest from overhead',
                'tags' => ['isolation', 'pull', 'vertical'],
            ],

            // LEGS
            [
                'name' => 'Squat',
                'aliases' => ['back squat', 'barbell squat', 'squats'],
                'category' => 'strength',
                'muscle_group' => 'legs',
                'equipment' => 'barbell',
                'difficulty' => 'intermediate',
                'description' => 'Bar on back, squat down and stand up',
                'tags' => ['compound', 'push', 'king_of_exercises'],
            ],
            [
                'name' => 'Front Squat',
                'aliases' => ['front squats'],
                'category' => 'strength',
                'muscle_group' => 'legs',
                'equipment' => 'barbell',
                'difficulty' => 'advanced',
                'description' => 'Bar on front shoulders, squat down',
                'tags' => ['compound', 'push', 'quad_focused'],
            ],
            [
                'name' => 'Leg Press',
                'aliases' => ['leg press machine'],
                'category' => 'strength',
                'muscle_group' => 'legs',
                'equipment' => 'machine',
                'difficulty' => 'beginner',
                'description' => 'Push weight sled with legs',
                'tags' => ['compound', 'push', 'machine'],
            ],
            [
                'name' => 'Romanian Deadlift',
                'aliases' => ['rdl', 'stiff leg deadlift', 'romanian dl'],
                'category' => 'strength',
                'muscle_group' => 'legs',
                'equipment' => 'barbell',
                'difficulty' => 'intermediate',
                'description' => 'Hip hinge movement, bar stays close to legs',
                'tags' => ['compound', 'pull', 'posterior', 'hamstrings'],
            ],
            [
                'name' => 'Lunges',
                'aliases' => ['walking lunges', 'lunge'],
                'category' => 'strength',
                'muscle_group' => 'legs',
                'equipment' => 'bodyweight',
                'difficulty' => 'beginner',
                'description' => 'Step forward and lower back knee',
                'tags' => ['compound', 'bodyweight', 'unilateral'],
            ],

            // SHOULDERS
            [
                'name' => 'Overhead Press',
                'aliases' => ['ohp', 'military press', 'shoulder press', 'press'],
                'category' => 'strength',
                'muscle_group' => 'shoulders',
                'equipment' => 'barbell',
                'difficulty' => 'intermediate',
                'description' => 'Press bar overhead from shoulders',
                'tags' => ['compound', 'push', 'vertical'],
            ],
            [
                'name' => 'Lateral Raise',
                'aliases' => ['side raise', 'dumbbell lateral raise', 'lateral raises'],
                'category' => 'strength',
                'muscle_group' => 'shoulders',
                'equipment' => 'dumbbell',
                'difficulty' => 'beginner',
                'description' => 'Raise dumbbells to sides',
                'tags' => ['isolation', 'side_delts'],
            ],
            [
                'name' => 'Face Pull',
                'aliases' => ['face pulls', 'cable face pull'],
                'category' => 'strength',
                'muscle_group' => 'shoulders',
                'equipment' => 'cable',
                'difficulty' => 'beginner',
                'description' => 'Pull rope to face level',
                'tags' => ['isolation', 'rear_delts', 'prehab'],
            ],

            // ARMS
            [
                'name' => 'Barbell Curl',
                'aliases' => ['bb curl', 'bicep curl', 'barbell curls'],
                'category' => 'strength',
                'muscle_group' => 'arms',
                'equipment' => 'barbell',
                'difficulty' => 'beginner',
                'description' => 'Curl bar towards shoulders',
                'tags' => ['isolation', 'pull', 'biceps'],
            ],
            [
                'name' => 'Tricep Dips',
                'aliases' => ['dips', 'tricep dip'],
                'category' => 'strength',
                'muscle_group' => 'arms',
                'equipment' => 'bodyweight',
                'difficulty' => 'intermediate',
                'description' => 'Lower body between parallel bars',
                'tags' => ['compound', 'push', 'bodyweight', 'triceps'],
            ],
            [
                'name' => 'Hammer Curl',
                'aliases' => ['hammer curls', 'neutral grip curl'],
                'category' => 'strength',
                'muscle_group' => 'arms',
                'equipment' => 'dumbbell',
                'difficulty' => 'beginner',
                'description' => 'Curl with palms facing each other',
                'tags' => ['isolation', 'biceps'],
            ],
            [
                'name' => 'Skull Crusher',
                'aliases' => ['lying tricep extension', 'skullcrushers'],
                'category' => 'strength',
                'muscle_group' => 'arms',
                'equipment' => 'barbell',
                'difficulty' => 'intermediate',
                'description' => 'Lower bar to forehead while lying',
                'tags' => ['isolation', 'triceps'],
            ],

            // CORE
            [
                'name' => 'Plank',
                'aliases' => ['front plank', 'planks'],
                'category' => 'strength',
                'muscle_group' => 'core',
                'equipment' => 'bodyweight',
                'difficulty' => 'beginner',
                'description' => 'Hold push-up position on forearms',
                'tags' => ['isometric', 'bodyweight', 'stabilization'],
            ],
            [
                'name' => 'Hanging Leg Raise',
                'aliases' => ['leg raise', 'hanging knee raise'],
                'category' => 'strength',
                'muscle_group' => 'core',
                'equipment' => 'bodyweight',
                'difficulty' => 'advanced',
                'description' => 'Hang from bar, raise legs',
                'tags' => ['isolation', 'bodyweight', 'abs'],
            ],
            [
                'name' => 'Cable Crunch',
                'aliases' => ['kneeling cable crunch', 'rope crunch'],
                'category' => 'strength',
                'muscle_group' => 'core',
                'equipment' => 'cable',
                'difficulty' => 'beginner',
                'description' => 'Kneel and crunch with cable resistance',
                'tags' => ['isolation', 'abs'],
            ],

            // CARDIO
            [
                'name' => 'Running',
                'aliases' => ['run', 'jog', 'jogging'],
                'category' => 'cardio',
                'muscle_group' => 'full_body',
                'equipment' => 'bodyweight',
                'difficulty' => 'beginner',
                'description' => 'Continuous running',
                'tags' => ['cardio', 'endurance'],
            ],
            [
                'name' => 'Cycling',
                'aliases' => ['bike', 'biking', 'bicycle'],
                'category' => 'cardio',
                'muscle_group' => 'legs',
                'equipment' => 'other',
                'difficulty' => 'beginner',
                'description' => 'Stationary or outdoor cycling',
                'tags' => ['cardio', 'low_impact'],
            ],
            [
                'name' => 'Rowing',
                'aliases' => ['row machine', 'erg'],
                'category' => 'cardio',
                'muscle_group' => 'full_body',
                'equipment' => 'machine',
                'difficulty' => 'beginner',
                'description' => 'Rowing machine cardio',
                'tags' => ['cardio', 'full_body'],
            ],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }

        $this->command->info('✅ Created ' . count($exercises) . ' exercises');
    }
}
