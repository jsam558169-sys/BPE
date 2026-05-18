<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('return_records', function (Blueprint $table) {
            $table->id('return_id');

            $table->unsignedBigInteger('borrow_record_id');
            $table->foreign('borrow_record_id')
                  ->references('borrow_record_id')->on('borrow_records')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')
                  ->references('admin_id')->on('admins')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->date('return_date');
            $table->time('return_time');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // One borrow record can only have one return record
            $table->unique('borrow_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_records');
    }
};
