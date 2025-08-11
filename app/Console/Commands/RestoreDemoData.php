<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;



class RestoreDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores demo data from SQL and replaces image folder';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $this->info('🚀 Starting demo data restoration process...');

        // Step 1: Seed the demo database
        $this->info('🔄 Seeding demo database tables...');
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\DemoDataSeeder',
                '--force' => true // Force it to run in production if needed
            ]);

            // Output seed results
            $this->line(Artisan::output());
            $this->info('✅ Database seeding completed.');
        } catch (\Exception $e) {
            $this->error('❌ Seeding failed: ' . $e->getMessage());
            return 1;
        }

        // Step 2: Copy asset folders
        $this->info('📂 Copying asset folders...');
        $demoAssetBase = base_path('demo_images');
        $storageAssetBase = storage_path('app/public');

        // Remove old demo folder if it exists
        $oldDemoPath = base_path('demo');
        if (File::exists($oldDemoPath)) {
            File::deleteDirectory($oldDemoPath);
            $this->info('🗑️ Old "demo" folder removed.');
        }

        if (!File::exists($demoAssetBase)) {
            $this->error('❌ "demo" folder not found at: ' . $demoAssetBase);
            return 1;
        }

        $assetFolders = ['images', 'videonews', 'opinion', 'ad_image'];

        foreach ($assetFolders as $folder) {
            $demoFolder = $demoAssetBase . DIRECTORY_SEPARATOR . $folder;
            $storageFolder = $storageAssetBase . DIRECTORY_SEPARATOR . $folder;

            if (File::exists($demoFolder)) {
                if (File::exists($storageFolder)) {
                    File::deleteDirectory($storageFolder);
                }

                File::copyDirectory($demoFolder, $storageFolder);
                $this->info("✅ Folder copied: {$folder}");
            } else {
                $this->warn("⚠️ Skipped missing folder: {$folder}");
            }
        }

        // Step 3: Regenerate symbolic link
        $storageLink = public_path('storage');

        $this->info('🔁 Refreshing symbolic storage link...');

        // Check if storage symlink exists
        if (file_exists($storageLink) || is_link($storageLink)) {
            // Remove the existing symlink or folder
            if (is_link($storageLink) || is_file($storageLink)) {
                unlink($storageLink);
            } elseif (is_dir($storageLink)) {
                // If it is a directory, delete recursively or just rmdir if empty
                rmdir($storageLink);
            }
            $this->info('🗑️ Existing storage link removed.');
        }

        Artisan::call('storage:link');
        $this->line(Artisan::output());
        $this->info('🔗 Storage link refreshed.');

        $this->info('🎉 Demo restoration process complete!');
        return 0;
    }

}
