<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FixAttachmentFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachments:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Orchid attachment files missing from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing Orchid attachment files...');
        
        // Create necessary directories
        $this->createDirectories();

        // Get all attachments
        $attachments = Attachment::all();
        $this->info("Found {$attachments->count()} attachments in database");
        
        $fixed = 0;
        
        foreach ($attachments as $attachment) {
            $diskPath = $attachment->path . '/' . $attachment->physicalPath();
            $fullPath = storage_path('app/public/' . $diskPath);
            $exists = file_exists($fullPath);
            
            $this->line("Checking attachment ID: {$attachment->id}, Name: {$attachment->name}, Path: {$diskPath}");
            $this->line("File exists: " . ($exists ? 'Yes' : 'No'));
            
            if (!$exists) {
                // Create directory if it doesn't exist
                $directory = storage_path('app/public/' . $attachment->path);
                if (!file_exists($directory)) {
                    $this->line("Creating directory: {$directory}");
                    mkdir($directory, 0755, true);
                }
                
                // Try to find the file in various locations
                $possibleLocations = [
                    // Temporary location
                    storage_path('app/public/temp/' . $attachment->name),
                    storage_path('app/temp/' . $attachment->name),
                    // Upload location
                    storage_path('app/uploads/' . $attachment->physicalPath()),
                    storage_path('app/uploads/' . $attachment->name),
                    // Original location (Orchid default)
                    storage_path('app/' . $attachment->disk . '/' . $attachment->path . '/' . $attachment->physicalPath()),
                    // Try different hash format
                    storage_path('app/public/' . $attachment->path . '/' . md5($attachment->name . $attachment->id) . '.' . $attachment->extension)
                ];
                
                $found = false;
                foreach ($possibleLocations as $location) {
                    if (file_exists($location)) {
                        $this->info("Found file at: {$location}");
                        $this->info("Copying to: {$fullPath}");
                        
                        // Ensure target directory exists
                        $targetDir = dirname($fullPath);
                        if (!file_exists($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }
                        
                        copy($location, $fullPath);
                        $found = true;
                        $fixed++;
                        break;
                    }
                }
                
                if (!$found) {
                    $this->error("Could not find file for attachment ID: {$attachment->id}");
                }
            }
        }
        
        $this->info("Fixed {$fixed} attachments");
        $this->info('Done!');
    }
    
    /**
     * Create necessary directories for category attachments
     */
    private function createDirectories()
    {
        $directories = [
            'app/public/categories',
            'app/public/categories/descriptions',
        ];
        
        foreach ($directories as $dir) {
            $path = storage_path($dir);
            if (!file_exists($path)) {
                $this->line("Creating directory: {$path}");
                mkdir($path, 0755, true);
            }
        }
    }
}
