<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Subcategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Добавляем parent_id в таблицу categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            $table->index('parent_id');
        });

        // Переносим данные из subcategories в categories
        $subcategories = DB::table('subcategories')->get();
        
        foreach ($subcategories as $subcategory) {
            DB::table('categories')->insert([
                'parent_id' => $subcategory->category_id,
                'name' => $subcategory->name,
                'description' => $subcategory->description,
                'slug' => $subcategory->slug,
                'created_at' => $subcategory->created_at,
                'updated_at' => $subcategory->updated_at,
            ]);
        }

        // Обновляем ссылки на subcategory_id в таблице products
        if (Schema::hasColumn('products', 'subcategory_id')) {
            $products = DB::table('products')
                ->whereNotNull('subcategory_id')
                ->get();

            // Создаем маппинг старых subcategory_id на новые category_id
            $subcategoryMap = [];
            $subcategories = DB::table('subcategories')->get();
            
            foreach ($subcategories as $subcategory) {
                $newCategory = DB::table('categories')
                    ->where('parent_id', $subcategory->category_id)
                    ->where('name', $subcategory->name)
                    ->first();
                
                if ($newCategory) {
                    $subcategoryMap[$subcategory->id] = $newCategory->id;
                }
            }

            // Обновляем продукты
            foreach ($products as $product) {
                if (isset($subcategoryMap[$product->subcategory_id])) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['category_id' => $subcategoryMap[$product->subcategory_id]]);
                }
            }

            // Удаляем колонку subcategory_id из таблицы products
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['subcategory_id']);
                $table->dropColumn('subcategory_id');
            });
        }

        // Удаляем таблицу subcategories
        Schema::dropIfExists('subcategories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Восстанавливаем таблицу subcategories
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->unique(['category_id', 'slug']);
            $table->timestamps();
        });

        // Добавляем subcategory_id обратно в products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories');
        });

        // Пытаемся восстановить данные из categories с parent_id
        $childCategories = DB::table('categories')->whereNotNull('parent_id')->get();
        
        foreach ($childCategories as $childCategory) {
            $subcategoryId = DB::table('subcategories')->insertGetId([
                'category_id' => $childCategory->parent_id,
                'name' => $childCategory->name,
                'description' => $childCategory->description,
                'slug' => $childCategory->slug,
                'created_at' => $childCategory->created_at,
                'updated_at' => $childCategory->updated_at,
            ]);
            
            // Обновляем ссылки в products
            DB::table('products')
                ->where('category_id', $childCategory->id)
                ->update([
                    'category_id' => $childCategory->parent_id,
                    'subcategory_id' => $subcategoryId
                ]);
        }

        // Удаляем дочерние категории
        DB::table('categories')->whereNotNull('parent_id')->delete();

        // Удаляем колонку parent_id
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
