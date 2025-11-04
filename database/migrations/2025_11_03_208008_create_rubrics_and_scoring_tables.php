<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('rubric_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_id')->constrained();
            $table->string('name');
            $table->decimal('weight',3,2)->default(1);
            $table->integer('max_score')->default(10);
            $table->timestamps();
        });

        Schema::create('evaluators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('tenant_id')->constrained();
            $table->timestamps();
            $table->unique(['tenant_id','user_id']);
        });

        Schema::create('assessment_evaluators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained();
            $table->foreignId('evaluator_id')->constrained();
            $table->timestamps();
            $table->unique(['assessment_id','evaluator_id']);
        });

        Schema::create('criterion_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained();
            $table->foreignId('evaluator_id')->constrained();
            $table->foreignId('rubric_criterion_id')->constrained('rubric_criteria');
            $table->integer('score');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['attempt_id','evaluator_id','rubric_criterion_id'], 'uniq_score');
        });
    }
    public function down(): void {
        Schema::dropIfExists('criterion_scores');
        Schema::dropIfExists('assessment_evaluators');
        Schema::dropIfExists('evaluators');
        Schema::dropIfExists('rubric_criteria');
        Schema::dropIfExists('rubrics');
    }
};
