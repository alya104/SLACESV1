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
        // Only create the table if it doesn't exist
        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained();
                $table->foreignId('class_id')->constrained('classes');
                $table->enum('status', ['active', 'pending'])->default('pending');
                $table->date('enrollment_date');
                $table->integer('completion_percentage')->default(0);
                $table->timestamps();

                $table->unique(['user_id', 'class_id']);
            });
        } else {
            // If table exists, update it to make sure it has all the required columns
            Schema::table('enrollments', function (Blueprint $table) {
                // Add any missing columns
                if (!Schema::hasColumn('enrollments', 'status')) {
                    $table->enum('status', ['active', 'pending'])->default('pending');
                }
                if (!Schema::hasColumn('enrollments', 'enrollment_date')) {
                    $table->date('enrollment_date')->nullable();
                }
                if (!Schema::hasColumn('enrollments', 'completion_percentage')) {
                    $table->integer('completion_percentage')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
