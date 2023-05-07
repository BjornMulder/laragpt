<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagePromptsTable extends Migration
{
    public function up()
    {
        Schema::create('image_prompts', function (Blueprint $table) {
            $table->id();
            $table->text('prompt');
            $table->text('image_url');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('image_prompts');
    }
}
