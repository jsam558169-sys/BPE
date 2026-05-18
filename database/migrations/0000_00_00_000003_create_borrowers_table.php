<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id('borrower_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('email', 255)->unique();
            $table->string('contact_number', 20)->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // adds deleted_at column
        });

        Schema::create('password_reset_tokens_borrowers', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens_borrowers');
        Schema::dropIfExists('borrowers');
    }
};
