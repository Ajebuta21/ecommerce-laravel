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
            $table->text('cart');
            $table->string('subtotal');
            $table->string('total');
            $table->string('delivery');
            $table->unsignedBigInteger('user_id');
            $table->string('user_firstname');
            $table->string('user_address');
            $table->string('user_phone');
            $table->string('region');
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
