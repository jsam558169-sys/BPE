<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('return_records', function (Blueprint $table) {
            $table->enum('condition', ['complete', 'incomplete', 'damaged'])
                ->default('complete')
                ->after('remarks');
        });
    }
    public function down(): void
    {
        Schema::table('return_records', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }
};
