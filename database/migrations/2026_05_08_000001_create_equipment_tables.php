<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name', 150)->unique();
            $table->timestamps();
        });

        Schema::create('equipment', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->foreignId('category_id')
                  ->constrained('equipment_categories', 'category_id')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')
                  ->references('admin_id')->on('admins')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('equipment_name', 200);
            $table->unsignedInteger('total_quantity')->default(0);
            $table->unsignedInteger('available_quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('equipment_categories');
    }
};
