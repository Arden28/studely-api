<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{Tenant, User, Student, Module, Assessment, Question, Option};
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // --- Tenant ---
            $tenant = Tenant::firstOrCreate(
                ['code' => 'demo'],
                ['name' => 'Demo College']
            );

            // --- Admin user within tenant ---
            $admin = User::firstOrCreate(
                ['email' => 'admin@college.test'],
                [
                    'name'      => 'College Admin',
                    'password'  => Hash::make('Password!234'),
                    'tenant_id' => $tenant->id,
                ]
            );
            // assign role if spatie/laravel-permission is present
            if (method_exists($admin, 'assignRole')) {
                $admin->assignRole('CollegeAdmin');
            }

            // --- Students + their Users (linked via user_id) ---
            foreach (range(1, 10) as $i) {
                $email = "student{$i}@college.test";
                $name  = "Student {$i}";

                // create/find the student "user"
                $studentUser = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'      => $name,
                        'password'  => Hash::make('Password!234'),
                        'tenant_id' => $tenant->id,
                    ]
                );
                if (method_exists($studentUser, 'assignRole')) {
                    $studentUser->assignRole('Student');
                }

                // create/find the Student model linked to that user
                $regNo = sprintf('REG-%03d', $i);
                Student::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'user_id'   => $studentUser->id, // <— link!
                    ],
                    [
                        'reg_no' => $regNo,
                        'branch' => 'Main',
                        'cohort' => '2025',
                    ]
                );
            }

            // --- Module ---
            $module = Module::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'title'     => 'Aptitude Basics',
                ],
                [
                    'per_student_time_limit_min' => 30,
                ]
            );

            // --- Assessment (MCQ) ---
            $ass = Assessment::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'module_id' => $module->id,
                    'type'      => 'MCQ',
                    'title'     => 'Aptitude Test – Set A',
                ],
                [
                    'instructions' => 'Pick the correct answer',
                    'total_marks'  => 10,
                ]
            );

            // --- Questions + Options (idempotent) ---
            foreach (range(1, 10) as $n) {
                $stem = "What is {$n}+{$n}?";

                $q = Question::firstOrCreate(
                    [
                        'tenant_id'     => $tenant->id,
                        'assessment_id' => $ass->id,
                        'stem'          => $stem,
                    ],
                    [
                        'type'       => 'MCQ',
                        'difficulty' => 'easy',
                        'topic'      => 'math',
                        'tags'       => ['demo', 'sum'],
                    ]
                );

                // correct
                Option::firstOrCreate(
                    ['question_id' => $q->id, 'label' => (string)($n + $n)],
                    ['is_correct' => true]
                );

                // distractors
                Option::firstOrCreate(
                    ['question_id' => $q->id, 'label' => (string)($n + $n + 1)],
                    ['is_correct' => false]
                );
                Option::firstOrCreate(
                    ['question_id' => $q->id, 'label' => (string)($n + $n - 1)],
                    ['is_correct' => false]
                );
            }
        });
    }
}
