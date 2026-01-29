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
        Schema::disableForeignKeyConstraints();

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_date')->default(now());
            $table->dateTime('end_date');
            $table->dateTime('trial_ends_at')->nullable();
            $table->enum('status', ["active", "cancelled", "expired", "pending"])->default('pending');
            $table->dateTime('cancelled_at')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_reference')->nullable()->unique();
            $table->string('mpesa_transaction_id')->nullable();
            $table->timestamps();

            // Add indexes for better query performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'end_date']);
        });
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
