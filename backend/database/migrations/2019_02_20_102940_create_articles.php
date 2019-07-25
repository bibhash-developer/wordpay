<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('company_id');
            $table->string('domain');
            $table->string('wordpress_article_type')->nullable();
            $table->integer('wordpress_post_id');
            $table->string('wordpress_post_url');
            $table->integer('coins_used');
            $table->integer('coins_balance');


            //$table->enum('package_type', ['package', 'subscription'])->default('package');
            //$table->double('coins', 10, 3);

            $table->dateTime('read_at')->nullable();
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
        Schema::dropIfExists('article_transactions');
    }
}
