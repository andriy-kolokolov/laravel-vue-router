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
        Schema::create('cocktail_ingredient', function (Blueprint $table) {
            $table->unsignedBigInteger('cocktail_id');
            $table->unsignedBigInteger('ingredient_id');
            // Add any other fields related to the pivot table if needed.

            // Foreign key constraints
            $table->foreign('cocktail_id')->references('id')->on('cocktails')->onDelete('cascade');
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cocktail_ingredient');
    }
};
