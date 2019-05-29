<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContentToLongtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("blog_posts", function (Blueprint $table) {
            $table->longText("content")->default("")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("blog_posts", function (Blueprint $table) {
            $table->longText("text")->default("")->change();
        });
    }
}
