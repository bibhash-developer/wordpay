<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_name');
            $table->enum('package_type', ['package', 'subscription'])->default('package');
            $table->integer('currency_id');
            $table->double('coins', 10, 3);
            $table->double('price', 10, 3);
            $table->double('discount', 10, 3);
            $table->dateTime('discount_schedule');
            $table->string('color_code', 7);
            $table->timestamp('published_at')->nullable();
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
        Schema::dropIfExists('packages');
    }
}
