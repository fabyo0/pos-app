<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if ( ! Schema::hasTable($tableName)) {
            return;
        }

        // Check for columns outside the closure for better database compatibility
        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            // Add device_id column if it doesn't exist
            if ( ! Schema::hasColumn($tableName, 'device_id')) {
                $table->string('device_id')->nullable()->index()->after('user_agent');
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            // Add device_name column if it doesn't exist
            if ( ! Schema::hasColumn($tableName, 'device_name')) {
                $table->string('device_name')->nullable()->after('device_id');
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            // Add is_trusted column if it doesn't exist
            if ( ! Schema::hasColumn($tableName, 'is_trusted')) {
                $table->boolean('is_trusted')->default(false)->after('device_name');
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            // Add last_activity_at column if it doesn't exist
            if ( ! Schema::hasColumn($tableName, 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('logout_at');
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            // Add is_suspicious column if it doesn't exist
            if ( ! Schema::hasColumn($tableName, 'is_suspicious')) {
                $table->boolean('is_suspicious')->default(false)->after('location');
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            // Add suspicious_reason column if it doesn't exist
            if ( ! Schema::hasColumn($tableName, 'suspicious_reason')) {
                $table->string('suspicious_reason')->nullable()->after('is_suspicious');
            }
        });
    }

    public function down(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if ( ! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            $columns = [
                'device_id',
                'device_name',
                'is_trusted',
                'last_activity_at',
                'is_suspicious',
                'suspicious_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
