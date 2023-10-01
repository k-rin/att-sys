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
        Schema::create('user_calendars', function (Blueprint $table) {
            $table->integer('user_id');
            $table->date('date');
            $table->boolean('holiday');
            $table->string('note', 64)->nullable();
            $table->timestamps();
            $table->primary(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_calendars');
    }
};