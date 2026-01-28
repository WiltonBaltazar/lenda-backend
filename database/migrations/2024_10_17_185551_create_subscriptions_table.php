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
        });

        // Add indexes for better query performance
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!$this->hasIndex('subscriptions', 'subscriptions_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }

            if (!$this->hasIndex('subscriptions', 'subscriptions_status_end_date_index')) {
                $table->index(['status', 'end_date']);
            }

            if (!$this->hasIndex('subscriptions', 'subscriptions_payment_reference_index')) {
                $table->index('payment_reference');
            }
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

    /*
     * Check if an index exists
     */
    private function hasIndex(string $table, string $index): bool
    {
        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);

        return array_key_exists($index, $indexes);
    }
};
