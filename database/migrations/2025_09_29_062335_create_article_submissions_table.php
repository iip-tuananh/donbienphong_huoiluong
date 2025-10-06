<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id');
            $table->string('author_name');
            $table->string('title');
            $table->longText('content_html')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending')->index();
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
        Schema::dropIfExists('article_submissions');
    }
}
