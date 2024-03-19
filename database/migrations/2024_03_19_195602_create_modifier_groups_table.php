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
        Schema::create('modifiergroups', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('modifier_group_name')->nullable();
            $table->string('modifier_group_desc')->nullable();
            $table->integer('modifier_group_type')->nullable();
            $table->integer('restaurant_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modifier_groups');
    }
};
