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
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('description')->after('title');
            $table->string('location')->after('description');
            $table->decimal('salary', 10, 2)->nullable()->after('location');
            $table->enum('type', ['full_time', 'part_time', 'internship', 'contract'])->default('full_time')->after('salary');
            $table->foreignId('company_id')->constrained()->onDelete('cascade')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            table->dropForeign(['company_id']);
            $table->dropColumn(['title', 'description', 'location', 'salary', 'type', 'company_id']);
        });
    }
};
