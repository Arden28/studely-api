<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Tenant, User, Student, Module, Assessment, Question, Option};

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(['code'=>'demo'],['name'=>'Demo College']);

        // College admin within tenant
        $admin = User::firstOrCreate(['email'=>'admin@college.test'], [
            'name'=>'College Admin',
            'password'=>bcrypt('Password!234'),
            'tenant_id'=>$tenant->id
        ]);
        $admin->assignRole('CollegeAdmin');

        // Students
        foreach (range(1,10) as $i) {
            Student::firstOrCreate([
                'tenant_id'=>$tenant->id,
                'reg_no'=>"REG-{$i}"
            ], [
                'branch'=>'Main', 'cohort'=>'2025'
            ]);
        }

        // Module
        $module = Module::firstOrCreate([
            'tenant_id'=>$tenant->id, 'title'=>'Aptitude Basics'
        ], [
            'per_student_time_limit_min'=>30
        ]);

        // Assessment (MCQ)
        $ass = Assessment::firstOrCreate([
            'tenant_id'=>$tenant->id,
            'module_id'=>$module->id,
            'type'=>'MCQ',
            'title'=>'Aptitude Test – Set A'
        ], [
            'instructions'=>'Pick the correct answer',
            'total_marks'=>10
        ]);

        // Questions
        foreach (range(1,10) as $n) {
            $q = Question::create([
                'tenant_id'=>$tenant->id,
                'assessment_id'=>$ass->id,
                'type'=>'MCQ',
                'stem'=>"What is {$n}+{$n}?",
                'difficulty'=>'easy',
                'topic'=>'math',
                'tags'=>['demo','sum']
            ]);
            Option::create(['question_id'=>$q->id,'label'=> (string)($n+$n),'is_correct'=>true]);
            Option::create(['question_id'=>$q->id,'label'=> (string)($n+$n+1),'is_correct'=>false]);
            Option::create(['question_id'=>$q->id,'label'=> (string)($n+$n-1),'is_correct'=>false]);
        }
    }
}
