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
        Schema::table('student_sub_indicators', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_term_id');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_sub_indicators', function (Blueprint $table) {
            $table->dropColumn(['academic_term_id']);
        });
    }
};
