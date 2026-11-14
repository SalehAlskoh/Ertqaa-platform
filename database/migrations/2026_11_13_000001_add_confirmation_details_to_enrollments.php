<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->dateTime('paid_at')->nullable();
        });
        DB::statement('ALTER TABLE enrollments ADD CONSTRAINT enrollments_paid_nonnegative CHECK (paid_amount IS NULL OR paid_amount >= 0)');
    }

    public function down(): void
    {
        throw new RuntimeException('Confirmation history must be preserved. This additive migration cannot be rolled back automatically.');
    }
};
