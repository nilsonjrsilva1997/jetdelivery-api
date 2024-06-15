<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('restaurant_id');
            $table->integer('order_status_id');
            $table->integer('delivery_address_id');
            $table->dateTime('delivery_date');
            $table->string('tracking_code')->nullable();
            $table->integer('payment_method_id');
            $table->integer('delivery_people_id')->nullable();
            $table->integer('estimated_delivery_time')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('delivery_fee', 8, 2)->nullable();
            $table->decimal('company_fee', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
