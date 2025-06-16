<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('research_area');
            $table->string('status')->default('pending');
            $table->text('feedback')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->enum('created_by', ['lecturer', 'student'])->default('student');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('topics');
    }
}; 