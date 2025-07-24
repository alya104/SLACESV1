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
        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description');
                $table->string('thumbnail')->nullable();
                $table->decimal('price', 10, 2);
                $table->enum('status', ['active', 'inactive', 'upcoming'])->default('active');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();
            });
        } else {
            // If table exists, update it to make sure it has all the required columns
            Schema::table('classes', function (Blueprint $table) {
                // Add any missing columns
                if (!Schema::hasColumn('classes', 'status')) {
                    $table->enum('status', ['active', 'inactive', 'upcoming'])->default('active');
                }
                if (!Schema::hasColumn('classes', 'start_date')) {
                    $table->date('start_date')->nullable();
                }
                if (!Schema::hasColumn('classes', 'end_date')) {
                    $table->date('end_date')->nullable();
                }
                if (!Schema::hasColumn('classes', 'thumbnail')) {
                    $table->string('thumbnail')->nullable()->after('description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
