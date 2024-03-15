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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('order_id');//->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id');//->constrained('customers')->onDelete('cascade');
            $table->string('table_id');
            $table->string('order_number');
            $table->string('restaurant_id');
            $table->string('payment_type');
            $table->string('payment_method')->nullable();
            $table->decimal('amount', 8, 2);
            $table->string('status');
            $table->string('transaction_id')->nullable();
            $table->string('payment_details')->nullable();            
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
        Schema::dropIfExists('order_payments');
    }
};
