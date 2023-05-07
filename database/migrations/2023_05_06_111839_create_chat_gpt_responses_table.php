<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chat_gpt_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_gpt_prompt_id');
            $table->text('response');
            $table->string('response_id');
            $table->string('object');
            $table->unsignedBigInteger('created');
            $table->string('model');
            $table->unsignedInteger('prompt_tokens');
            $table->unsignedInteger('completion_tokens');
            $table->unsignedInteger('total_tokens');
            $table->string('finish_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_gpt_responses');
    }
};
