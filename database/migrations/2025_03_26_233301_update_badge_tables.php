<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->enum('type', ['student', 'mentor', 'general'])->default('general');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('badge_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('badge_id');
            $table->string('rule_type');
            $table->string('condition');
            $table->string('operator');
            $table->string('threshold_value');
            $table->boolean('is_mandatory')->default(true);

            $table->foreign('badge_id')
                  ->references('id')
                  ->on('badges')
                  ->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('badge_id');
            $table->timestamp('achievement_date')->useCurrent();
            $table->enum('status', ['earned', 'in_progress', 'locked'])->default('locked');
            $table->json('achievement_details')->nullable(); 
            $table->integer('progress_percentage')->default(0);

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('badge_id')
                  ->references('id')
                  ->on('badges')
                  ->onDelete('cascade');

            $table->unique(['user_id', 'badge_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badge_rules');
        Schema::dropIfExists('badges');
    }
};
