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
        Schema::create('table_actives', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('table_id');
            $table->integer('table_number');
            $table->integer('divided_by')->nullable();
            $table->string('split_table_number')->nullable();
            $table->integer('section_id');
            $table->string('section_name')->nullable();
            $table->integer('floor_number');
            $table->integer('restaurant_id');
            $table->integer('cover_count')->nullable();
            $table->string('status')->nullable();       
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
        Schema::dropIfExists('table_actives');
    }
};
