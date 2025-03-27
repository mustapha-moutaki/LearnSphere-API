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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_courses_completed')->default(0);
            $table->integer('unique_courses_taken')->default(0);
        $table->integer('total_active_months')->default(0);
            
        $table->integer('total_published_courses')->default(0);
            $table->integer('total_enrolled_students')->default(0);
            $table->integer('profile_completion_percentage')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
