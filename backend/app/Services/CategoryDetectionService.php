<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryDetectionService
{
    /**
     * Определяет категорию и подкатегорию для продукта на основе данных и LocalLLM
     *
     * @param array $productData Данные о продукте
     * @return array С ключами 'category_id' и 'subcategory_id'
     */
    public function detectCategoryAndSubcategory(array $productData): array
    {
        try {
            // Формируем контекст для LLM из данных о продукте
            $context = $this->prepareContext($productData);
            
            // Получаем предсказание от LLM
            $prediction = $this->getLlmPrediction($context);
            
            // Обрабатываем ответ и создаем/находим категории в базе данных
            return $this->processPrediction($prediction);
        } catch (\Exception $e) {
            Log::error('Ошибка при определении категории: ' . $e->getMessage());
            // Возвращаем пустые ID в случае ошибки
            return [
                'category_id' => null,
                'subcategory_id' => null
            ];
        }
    }

    /**
     * Подготавливает контекст для запроса к LLM
     *
     * @param array $productData
     * @return string
     */
    private function prepareContext(array $productData): string
    {
        $name = $productData['название_товара'] ?? '';
        $description = $productData['описание'] ?? '';
        
        // Собираем характеристики товара из спецификаций
        $specs = '';
        if (isset($productData['спецификации']) && is_array($productData['спецификации'])) {
            foreach ($productData['спецификации'] as $categoryName => $categorySpecs) {
                $specs .= "- $categoryName:\n";
                if (is_array($categorySpecs)) {
                    foreach ($categorySpecs as $specName => $specValue) {
                        $specs .= "  * $specName: $specValue\n";
                    }
                }
            }
        }

        // Формируем контекст для LLM
        return <<<PROMPT
        Определи наиболее подходящую категорию и подкатегорию для следующего товара:
        
        Название товара: $name
        
        Описание товара: $description
        
        Характеристики товара:
        $specs
        
        Доступные категории товаров:
        - Инструмент
        - Крепеж
        - Строительные материалы
        - Электротовары
        - Садовый инвентарь
        - Сантехника
        - Лакокрасочные материалы
        - Хозяйственные товары
        - Освещение
        - Напольные покрытия
        - Другое
        
        Некоторые примеры подкатегорий:
        - Для категории "Инструмент": Шуруповерты, Дрели, Болгарки, Лобзики, Перфораторы, Пилы, Фрезеры
        - Для категории "Электротовары": Кабели, Розетки, Выключатели, Автоматы
        - Для категории "Садовый инвентарь": Лопаты, Грабли, Секаторы, Шланги, Газонокосилки
        
        Верни ответ СТРОГО в следующем формате JSON (без дополнительных комментариев):
        {"category": "Название категории", "subcategory": "Название подкатегории"}
        PROMPT;
    }

    /**
     * Получает предсказание от Local LLM
     *
     * @param string $context
     * @return string
     */
    private function getLlmPrediction(string $context): string
    {
        try {
            // Запускаем процесс ollama для получения ответа от LLM
            $descriptorspec = [
                0 => ["pipe", "r"],  // stdin
                1 => ["pipe", "w"],  // stdout
                2 => ["pipe", "w"]   // stderr
            ];
            
            $process = proc_open('ollama run llama2', $descriptorspec, $pipes);
            
            if (is_resource($process)) {
                // Отправляем запрос
                fwrite($pipes[0], $context);
                fclose($pipes[0]);
                
                // Получаем ответ
                $response = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                
                // Получаем ошибки, если есть
                $errors = stream_get_contents($pipes[2]);
                fclose($pipes[2]);
                
                // Закрываем процесс
                $return_value = proc_close($process);
                
                if ($return_value !== 0) {
                    Log::error("Ошибка выполнения Ollama: $errors");
                    throw new \Exception("Ошибка выполнения Ollama, код: $return_value");
                }
                
                return $response;
            } else {
                throw new \Exception("Не удалось запустить процесс Ollama");
            }
        } catch (\Exception $e) {
            Log::error('Ошибка при обращении к LLM: ' . $e->getMessage());
            
            // Фолбэк на базовые категории в случае ошибки
            return '{"category": "Другое", "subcategory": "Разное"}';
        }
    }

    /**
     * Обрабатывает предсказание LLM и создает/находит категории в БД
     *
     * @param string $prediction
     * @return array С ключами 'category_id' и 'subcategory_id'
     */
    private function processPrediction(string $prediction): array
    {
        try {
            // Извлекаем JSON из ответа LLM, находим первую фигурную скобку
            $jsonStart = strpos($prediction, '{');
            $jsonEnd = strrpos($prediction, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonString = substr($prediction, $jsonStart, $jsonEnd - $jsonStart + 1);
                $data = json_decode($jsonString, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Если не удалось декодировать, пытаемся извлечь данные регулярным выражением
                    preg_match('/"category"\s*:\s*"([^"]+)"/i', $prediction, $categoryMatches);
                    preg_match('/"subcategory"\s*:\s*"([^"]+)"/i', $prediction, $subcategoryMatches);
                    
                    $categoryName = $categoryMatches[1] ?? 'Другое';
                    $subcategoryName = $subcategoryMatches[1] ?? 'Разное';
                } else {
                    $categoryName = $data['category'] ?? 'Другое';
                    $subcategoryName = $data['subcategory'] ?? 'Разное';
                }
            } else {
                $categoryName = 'Другое';
                $subcategoryName = 'Разное';
            }
            
            // Нормализуем названия категорий
            $categoryName = $this->normalizeString($categoryName);
            $subcategoryName = $this->normalizeString($subcategoryName);
            
            // Находим или создаем категорию
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
            
            // Находим или создаем подкатегорию
            $subcategory = Subcategory::firstOrCreate(
                [
                    'category_id' => $category->id,
                    'name' => $subcategoryName
                ],
                ['slug' => Str::slug($subcategoryName)]
            );
            
            return [
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id
            ];
        } catch (\Exception $e) {
            Log::error('Ошибка при обработке ответа LLM: ' . $e->getMessage());
            return [
                'category_id' => null,
                'subcategory_id' => null
            ];
        }
    }
    
    /**
     * Нормализует строку (убирает лишние пробелы, приводит к title case и т.д.)
     *
     * @param string $str
     * @return string
     */
    private function normalizeString(string $str): string
    {
        // Убираем лишние пробелы
        $str = trim(preg_replace('/\s+/', ' ', $str));
        
        // Приводим к Title Case
        $str = ucwords(mb_strtolower($str));
        
        return $str;
    }
} 