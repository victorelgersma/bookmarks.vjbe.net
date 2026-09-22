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
        Schema::rename('links', 'bookmarks');

        Schema::table('bookmarks', function (Blueprint $table) {
            $table->renameColumn('description', 'name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->renameColumn('name', 'description');
        });

        Schema::rename('bookmarks', 'links');
    }
};
