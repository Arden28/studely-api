<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained();
            $table->foreignId('assessment_id')->nullable()->constrained();
            $table->enum('type',['MCQ','OPEN']);
            $table->text('stem');
            $table->string('difficulty')->nullable();
            $table->string('topic')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained();
            $table->string('label');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
    }
};
