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
        Schema::table('kot_items', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false);
            // $table->string('custom_name')->nullable();
            // $table->decimal('custom_price', 10, 2)->nullable();
            // $table->text('custom_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kot_items', function (Blueprint $table) {
            $table->dropColumn(['is_custom', 
            // 'custom_name', 'custom_price', 
            // 'custom_description'
        ]);
        });
    }
};
