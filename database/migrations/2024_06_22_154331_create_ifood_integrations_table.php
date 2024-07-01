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
        Schema::create('ifood_integrations', function (Blueprint $table) {
            $table->id();
            $table->boolean('active');
            $table->integer('restaurant_id');
            $table->string('authorization_code_verifier');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ifood_integrations');
    }
};
