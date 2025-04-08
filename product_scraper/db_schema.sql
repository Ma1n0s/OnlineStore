-- Схема базы данных для хранения товаров (SQLite)
-- Совместима с последующей миграцией на PostgreSQL

-- Таблица товаров
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,  -- В PostgreSQL будет: SERIAL PRIMARY KEY
    name TEXT NOT NULL,
    description TEXT,
    product_url TEXT,
    search_market_url TEXT,
    search_images_url TEXT,
    created_at TEXT,  -- В PostgreSQL будет: TIMESTAMP
    updated_at TEXT DEFAULT (datetime('now'))  -- В PostgreSQL будет: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица категорий характеристик
CREATE TABLE IF NOT EXISTS specification_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,  -- В PostgreSQL будет: SERIAL PRIMARY KEY
    product_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE (product_id, name)
);

-- Таблица спецификаций
CREATE TABLE IF NOT EXISTS specifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,  -- В PostgreSQL будет: SERIAL PRIMARY KEY
    category_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    value TEXT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES specification_categories(id) ON DELETE CASCADE,
    UNIQUE (category_id, name)
);

-- Таблица изображений
CREATE TABLE IF NOT EXISTS images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,  -- В PostgreSQL будет: SERIAL PRIMARY KEY
    product_id INTEGER NOT NULL,
    url TEXT NOT NULL,
    source TEXT NOT NULL,  -- В PostgreSQL будет: ENUM('market', 'yandex')
    position INTEGER NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE (product_id, source, position)
);

-- Индексы для оптимизации запросов
CREATE INDEX IF NOT EXISTS idx_products_name ON products(name);
CREATE INDEX IF NOT EXISTS idx_specification_categories_product_id ON specification_categories(product_id);
CREATE INDEX IF NOT EXISTS idx_specifications_category_id ON specifications(category_id);
CREATE INDEX IF NOT EXISTS idx_images_product_id ON images(product_id);

-- Миграция на PostgreSQL:
-- 1. Заменить INTEGER PRIMARY KEY AUTOINCREMENT на SERIAL PRIMARY KEY
-- 2. Заменить TEXT DEFAULT (datetime('now')) на TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- 3. Заменить TEXT на VARCHAR(255) где это необходимо
-- 4. Использовать ENUM для поля source в таблице images 