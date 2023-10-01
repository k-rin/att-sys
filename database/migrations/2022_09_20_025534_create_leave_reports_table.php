<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->float('days');
            $table->tinyInteger('type');
            $table->string('reason', 256)->nullable();
            $table->string('note', 256)->nullable();
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_reports');
    }
};