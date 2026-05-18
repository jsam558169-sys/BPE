<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('borrow_records', function (Blueprint $table) {
            $table->id('borrow_record_id');

            $table->unsignedBigInteger('borrower_id');
            $table->foreign('borrower_id')
                  ->references('borrower_id')->on('borrowers')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')
                  ->references('admin_id')->on('admins')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')
                  ->references('status_id')->on('statuses')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->date('borrow_date');
            $table->time('check_out_time');
            $table->date('expected_return_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_records');
    }
};
