<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateTestDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data 
                            {--products=10 : Number of products to generate} 
                            {--categories : Generate categories}
                            {--clear : Clear all existing data first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for products and categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            $this->clearData();
        }

        if ($this->option('categories')) {
            $this->generateCategories();
        }

        $this->generateProducts($this->option('products'));

        $this->info('Test data generated successfully!');
    }

    protected function createSubcategories($parent, array $subs)
    {
        foreach ($subs as $sub) {
            Category::create([
                'name' => $sub,
                'slug' => Str::slug($sub),
                'parent_id' => $parent->id,
            ]);
        }
    }

    protected function generateProducts($count)
    {
        $categories = Category::pluck('id');
        
        if ($categories->isEmpty()) {
            $this->error('No categories found! Please generate categories first with --categories option');
            return;
        }

        $types = ['preorder', 'rent', 'instock'];
        $brands = ['TestBrand', 'SampleBrand', 'DemoBrand'];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 1; $i <= $count; $i++) {
            Product::create([
                'name' => 'Тестовый товар ' . $i,
                'code' => 'TEST-' . rand(1000, 9999),
                'article' => 'ART-' . rand(10000, 99999),
                'brand' => $brands[array_rand($brands)],
                'type' => $types[array_rand($types)],
                'price' => rand(100, 10000),
                'quantity' => rand(0, 100),
                'description' => 'Это автоматически сгенерированный тестовый товар',
                'delivery_days' => rand(1, 14),
                'category_id' => $categories->random(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Products generated: ' . $count);
    }

    protected function clearData()
    {
        Product::truncate();
        Category::truncate();
        $this->info('All products and categories cleared!');
    }
}