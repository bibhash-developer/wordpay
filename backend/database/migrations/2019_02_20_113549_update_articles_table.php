<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_transactions', function (Blueprint $table) {
            $table->double('wordpress_max_price', 10, 3)->after('wordpress_post_url');
            $table->double('wordpress_min_price', 10, 3)->after('wordpress_max_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_transactions', function (Blueprint $table) {
            $table->dropColumn('wordpress_max_price');
            $table->dropColumn('wordpress_min_price');
        });
    }
}
