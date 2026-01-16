<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

final class GeneratePermissions extends Command
{
    protected $signature = 'permissions:generate';

    protected $description = 'Generate permissions from config file';

    public function handle(): int
    {
        $this->info('🔄 Generating permissions...');

        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $resources = config('permission-resources');
        $count = 0;

        foreach ($resources as $resource => $config) {
            foreach ($config['permissions'] as $action) {
                $permission = Permission::firstOrCreate([
                    'name' => "{$resource}.{$action}",
                    'guard_name' => 'web',
                ]);

                if ($permission->wasRecentlyCreated) {
                    $this->info("✅ Created: {$resource}.{$action}");
                    $count++;
                } else {
                    $this->comment("⏭️  Exists: {$resource}.{$action}");
                }
            }
        }

        $this->newLine();
        $this->info("✨ Generated {$count} new permissions!");

        return self::SUCCESS;
    }
}
