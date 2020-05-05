<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogPostFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_post_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("site_id")->nullable();
            $table->unsignedInteger('post_id');
            $table->string('path');
            $table->timestamps();

            $table->foreign("blog_post_id")
                ->references("id")
                ->on("blog_posts");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_post_files');
    }
}
