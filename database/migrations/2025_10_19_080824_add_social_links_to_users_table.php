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
            $table->string('website')->nullable()->after('bio');
            $table->string('instagram')->nullable()->after('website');
            $table->string('youtube')->nullable()->after('instagram');
            $table->string('facebook')->nullable()->after('youtube');
            $table->string('twitter')->nullable()->after('facebook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['website', 'instagram', 'youtube', 'facebook', 'twitter']);
        });
    }
};
