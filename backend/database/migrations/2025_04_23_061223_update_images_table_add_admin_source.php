<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table since ALTER COLUMN with ENUM is not supported
        if (DB::connection()->getDriverName() === 'sqlite') {
            // First, create a backup of the current data
            $images = DB::table('images')->get();
            
            // Drop constraints to enable table recreation
            Schema::table('images', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropUnique(['product_id', 'source', 'position']);
            });
            
            // Drop the table
            Schema::dropIfExists('images');
            
            // Recreate the table with updated enum values
            Schema::create('images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('url');
                $table->enum('source', ['market', 'yandex', 'admin']);
                $table->integer('position');
                $table->unique(['product_id', 'source', 'position']);
                $table->timestamps();
            });
            
            // Reinsert the data
            foreach ($images as $image) {
                DB::table('images')->insert([
                    'id' => $image->id,
                    'product_id' => $image->product_id,
                    'url' => $image->url,
                    'source' => $image->source === 'admin' ? 'market' : $image->source, // Convert 'admin' to valid value
                    'position' => $image->position,
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at,
                ]);
            }
        } else {
            // For MySQL or PostgreSQL, we can alter the column
            DB::statement("ALTER TABLE images MODIFY COLUMN source ENUM('market', 'yandex', 'admin')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table to revert changes
            // First, create a backup of the current data
            $images = DB::table('images')->get();
            
            // Drop constraints
            Schema::table('images', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropUnique(['product_id', 'source', 'position']);
            });
            
            // Drop the table
            Schema::dropIfExists('images');
            
            // Recreate the table with original enum values
            Schema::create('images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('url');
                $table->enum('source', ['market', 'yandex']);
                $table->integer('position');
                $table->unique(['product_id', 'source', 'position']);
                $table->timestamps();
            });
            
            // Reinsert the data
            foreach ($images as $image) {
                if ($image->source !== 'admin') {
                    DB::table('images')->insert([
                        'id' => $image->id,
                        'product_id' => $image->product_id,
                        'url' => $image->url,
                        'source' => $image->source,
                        'position' => $image->position,
                        'created_at' => $image->created_at,
                        'updated_at' => $image->updated_at,
                    ]);
                }
            }
        } else {
            // For MySQL or PostgreSQL
            DB::statement("ALTER TABLE images MODIFY COLUMN source ENUM('market', 'yandex')");
        }
    }
};
