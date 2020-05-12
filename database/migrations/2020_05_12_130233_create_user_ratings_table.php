<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_user_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('rating');
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('given_to');
            $table->unsignedBigInteger('taskId');
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
        Schema::dropIfExists('task_user_ratings');
    }
}
