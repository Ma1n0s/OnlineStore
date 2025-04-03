<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SpecificationCategory;
use App\Models\Specification;
use App\Models\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductImportService
{
    protected CategoryDetectionService $categoryDetectionService;

    public function __construct(CategoryDetectionService $categoryDetectionService)
    {
        $this->categoryDetectionService = $categoryDetectionService;
    }

    /**
     * Импортирует данные о продукте
     *
     * @param array $data Данные о продукте из ProductScraper
     * @return Product
     */
    public function importProduct(array $data): Product
    {
        // Используем транзакцию для обеспечения целостности данных
        return DB::transaction(function () use ($data) {
            // Создаем или обновляем продукт
            $product = Product::updateOrCreate(
                ['name' => $data['название_товара']],
                [
                    'description' => $data['описание'] ?? null,
                    'product_url' => $data['ссылки']['товар'] ?? null,
                    'search_market_url' => $data['ссылки']['поиск_маркет'] ?? null,
                    'search_images_url' => $data['ссылки']['поиск_картинки'] ?? null,
                    'created_at' => isset($data['время_запроса']) 
                        ? Carbon::createFromFormat('Y-m-d H:i:s', $data['время_запроса']) 
                        : now(),
                    'category_id' => $data['category_id'] ?? null,
                    'subcategory_id' => $data['subcategory_id'] ?? null,
                ]
            );

            // Импортируем спецификации
            if (isset($data['спецификации']) && is_array($data['спецификации'])) {
                $this->importSpecifications($product, $data['спецификации']);
            }

            // Импортируем изображения
            if (isset($data['изображения']) && is_array($data['изображения'])) {
                $this->importImages($product, $data['изображения']);
            }

            return $product;
        });
    }

    /**
     * Импортирует спецификации продукта
     *
     * @param Product $product
     * @param array $specifications
     * @return void
     */
    private function importSpecifications(Product $product, array $specifications): void
    {
        foreach ($specifications as $categoryName => $categorySpecs) {
            // Создаем или обновляем категорию
            $category = SpecificationCategory::firstOrCreate([
                'product_id' => $product->id,
                'name' => $categoryName,
            ]);

            // Импортируем спецификации категории
            if (is_array($categorySpecs)) {
                foreach ($categorySpecs as $name => $value) {
                    Specification::updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'name' => $name,
                        ],
                        ['value' => $value]
                    );
                }
            }
        }
    }

    /**
     * Импортирует изображения продукта
     *
     * @param Product $product
     * @param array $images
     * @return void
     */
    private function importImages(Product $product, array $images): void
    {
        // Удаляем существующие изображения
        $product->images()->delete();

        // Импортируем изображения из Яндекс.Маркета
        if (isset($images['маркет']) && is_array($images['маркет'])) {
            foreach ($images['маркет'] as $position => $url) {
                Image::create([
                    'product_id' => $product->id,
                    'url' => $url,
                    'source' => Image::SOURCES['market'],
                    'position' => $position,
                ]);
            }
        }

        // Импортируем изображения из Яндекс.Картинок
        if (isset($images['картинки']) && is_array($images['картинки'])) {
            foreach ($images['картинки'] as $position => $url) {
                Image::create([
                    'product_id' => $product->id,
                    'url' => $url,
                    'source' => Image::SOURCES['yandex'],
                    'position' => $position,
                ]);
            }
        }
    }
} 