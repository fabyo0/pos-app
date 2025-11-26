<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Customer::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignIdFor(PaymentMethod::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->decimal('total');
            $table->decimal('paid_amount');
            $table->decimal('discount')->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
