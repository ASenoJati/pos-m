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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('track');
            $table->string('name')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('subtotal')->nullable();
            $table->string('pay')->nullable();
            $table->string('return_pay')->nullable();
            $table->string('deficiency')->nullable();
            $table->string('hold_order')->nullable();
            $table->string('progress')->comment('1=Finish,2=On Process,3=Waiting by customer')->nullable();
            $table->dateTime('enter_order')->nullable();
            $table->dateTime('finished_order')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
