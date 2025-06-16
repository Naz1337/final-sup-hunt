<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->text('expertise')->nullable()->after('photo');
            $table->text('teaching_experience')->nullable()->after('expertise');
            $table->text('previous_fyp_titles')->nullable()->after('teaching_experience');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn(['expertise', 'teaching_experience', 'previous_fyp_titles']);
        });
    }
};
