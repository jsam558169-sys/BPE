<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('borrow_record_equipment', function (Blueprint $table) {
            $table->id('borrow_record_equipment_id');

            $table->unsignedBigInteger('borrow_record_id');
            $table->foreign('borrow_record_id')
                  ->references('borrow_record_id')->on('borrow_records')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // cascade: deleting a borrow record removes its line items

            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')
                  ->references('equipment_id')->on('equipment')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->unsignedInteger('quantity_borrowed')->default(1);

            // Prevent duplicate equipment entries per borrow record
            $table->unique(['borrow_record_id', 'equipment_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_record_equipment');
    }
};
