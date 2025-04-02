import os
import json
import pandas as pd
from bs4 import BeautifulSoup
import requests
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from typing import Dict, List, Optional
import time
import logging
import subprocess
from urllib.parse import quote

# Настройка логирования
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

class LocalLLM:
    def __init__(self, model_name: str = "llama2"):
        """Инициализация локальной модели через Ollama"""
        self.model_name = model_name
        self._ensure_model_pulled()
        
    def _ensure_model_pulled(self):
        """Проверяет, загружена ли модель, и загружает её при необходимости"""
        try:
            # Проверяем, есть ли модель в списке
            result = subprocess.run(['ollama', 'list'], capture_output=True, text=True)
            if self.model_name in result.stdout:
                logging.info(f"Модель {self.model_name} уже загружена")
                return
                
            logging.info(f"Загружаем модель {self.model_name}...")
            subprocess.run(['ollama', 'pull', self.model_name], check=True)
        except subprocess.CalledProcessError as e:
            logging.error(f"Ошибка при проверке/загрузке модели: {str(e)}")
            raise
        
    def get_completion(self, prompt: str) -> str:
        """Получение ответа от локальной модели"""
        try:
            result = subprocess.run(
                ['ollama', 'run', self.model_name, prompt],
                capture_output=True,
                text=True,
                check=True
            )
            return result.stdout.strip()
        except subprocess.CalledProcessError as e:
            logging.error(f"Ошибка при получении ответа от модели: {str(e)}")
            return ""

class ProductScraper:
    def __init__(self, model_name: str = "llama2"):
        self.llm = LocalLLM(model_name)
        self.setup_selenium()
        
    def setup_selenium(self):
        """Настройка Selenium для динамического скрапинга"""
        chrome_options = Options()
        chrome_options.add_argument("--headless=new")
        chrome_options.add_argument("--no-sandbox")
        chrome_options.add_argument("--disable-dev-shm-usage")
        chrome_options.add_argument("--lang=ru")
        chrome_options.add_argument("--disable-blink-features=AutomationControlled")
        chrome_options.add_argument("--user-agent=Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36")
        
        # Дополнительные оптимизации
        chrome_options.add_argument("--disable-gpu")
        chrome_options.add_argument("--disable-software-rasterizer")
        chrome_options.add_argument("--disable-extensions")
        chrome_options.add_argument("--disable-notifications")
        chrome_options.add_argument("--disable-logging")
        chrome_options.add_argument("--disable-infobars")
        chrome_options.add_argument("--disable-web-security")
        chrome_options.add_argument("--disable-features=IsolateOrigins,site-per-process")
        chrome_options.add_argument("--disk-cache-size=0")
        chrome_options.page_load_strategy = 'eager'  # Не ждем полной загрузки страницы
        
        # Дополнительные настройки для обхода защиты
        chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
        chrome_options.add_experimental_option('useAutomationExtension', False)
        
        try:
            self.driver = webdriver.Chrome(options=chrome_options)
            self.driver.set_page_load_timeout(30)  # Таймаут загрузки страницы
            self.driver.implicitly_wait(5)  # Уменьшаем время ожидания элементов
            
            self.driver.execute_cdp_cmd('Network.setUserAgentOverride', {
                "userAgent": 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36'
            })
            self.driver.execute_script("Object.defineProperty(navigator, 'webdriver', {get: () => undefined})")
        except Exception as e:
            logging.error(f"Ошибка при инициализации Chrome: {e}")
            raise

    def search_product(self, product_name: str) -> Dict:
        """
        Собирает информацию о товаре с Яндекс.Маркета и изображения с Яндекс.Картинок
        """
        try:
            market_query = quote(product_name)
            market_url = f"https://market.yandex.ru/search?text={market_query}&cvredirect=2"
            logging.info(f"\nЯндекс.Маркет поиск URL: {market_url}")
            
            self.driver.execute_cdp_cmd('Network.setExtraHTTPHeaders', {
                'headers': {
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                    'Accept-Language': 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Cache-Control': 'max-age=0',
                    'Sec-Ch-Ua': '"Not_A Brand";v="8", "Chromium";v="134"',
                    'Sec-Ch-Ua-Mobile': '?0',
                    'Sec-Ch-Ua-Platform': '"Linux"',
                    'Sec-Fetch-Dest': 'document',
                    'Sec-Fetch-Mode': 'navigate',
                    'Sec-Fetch-Site': 'none',
                    'Sec-Fetch-User': '?1',
                    'Upgrade-Insecure-Requests': '1'
                }
            })
            
            self.driver.get(market_url)
            time.sleep(3)  # Уменьшаем время ожидания
            
            if "Подтвердите, что запросы отправляли вы, а не робот" in self.driver.page_source:
                logging.error("Обнаружена капча!")
                return {
                    "название_товара": product_name,
                    "ошибка": "Сайт требует подтверждение, что вы не робот",
                    "время_запроса": time.strftime("%Y-%m-%d %H:%M:%S"),
                    "ссылка_на_поиск": market_url
                }
            
            product_selectors = [
                "a[href*='/product--']",
                "._2Qo3ODl0by",
                "article a[href*='/product/']",
                "[data-zone-name='snippet'] a"
            ]
            
            product_url = None
            for selector in product_selectors:
                try:
                    product_links = self.driver.find_elements("css selector", selector)
                    if product_links:
                        product_url = product_links[0].get_attribute("href")
                        break
                except Exception:
                    continue
            
            if not product_url:
                logging.warning("Не найдены ссылки на товары")
                return {
                    "название_товара": product_name,
                    "ошибка": "Не найдены ссылки на товары",
                    "время_запроса": time.strftime("%Y-%m-%d %H:%M:%S"),
                    "ссылка_на_поиск": market_url
                }
            
            logging.info(f"URL товара: {product_url}")
            
            self.driver.get(product_url)
            time.sleep(3)  # Уменьшаем время ожидания
            
            # Нажимаем на кнопку "Все характеристики"
            try:
                show_all_buttons = self.driver.find_elements("css selector", "[data-auto='read-more-button'], [data-zone-name='readMoreButton'] button")
                for button in show_all_buttons:
                    try:
                        if "характеристики" in button.text.lower():
                            button.click()
                            time.sleep(1)  # Ждем раскрытия характеристик
                            break
                    except:
                        continue
            except Exception as e:
                logging.warning(f"Не удалось нажать кнопку 'Все характеристики': {e}")
            
            product_data = {
                "характеристики": {},
                "функции": {},
                "общее": {},
                "дополнительно": {}
            }
            
            try:
                # Парсим основные характеристики
                spec_rows = self.driver.find_elements("css selector", "._3rW2x")
                
                for row in spec_rows:
                    try:
                        name_elem = row.find_element("css selector", "._6DaYY")
                        value_elem = row.find_element("css selector", ".b2ZT4")
                        
                        if name_elem and value_elem:
                            name = name_elem.text.strip()
                            value = value_elem.text.strip()
                            
                            # Пропускаем пустые значения
                            if not name or not value or name == ":" or value == ":":
                                continue
                            
                            # Распределяем характеристики по категориям
                            if any(word in name.lower() for word in ["мощность", "напряжение", "вес", "размер", "габарит", "диаметр", "частота", "скорость", "сила", "удар", "патрон", "число", "оборот", "длина", "шаг", "артикул", "бренд", "производитель"]):
                                product_data["характеристики"][name] = value
                            elif any(word in name.lower() for word in ["режим", "функция", "защита", "система", "реверс", "регулировка", "плавный пуск"]):
                                product_data["функции"][name] = value
                            elif any(word in name.lower() for word in ["комплект", "поставка", "упаковка", "кейс", "чемодан", "в комплекте", "особенность", "конструкция"]):
                                product_data["общее"][name] = value
                            elif any(word in name.lower() for word in ["гарантия", "срок", "сертификат", "страна"]):
                                product_data["дополнительно"][name] = value
                            else:
                                product_data["характеристики"][name] = value
                                
                    except Exception as e:
                        logging.debug(f"Ошибка при обработке строки характеристики: {e}")
                        continue
                
                # Парсим описание товара
                description_selectors = [
                    "[data-auto='product-description']",
                    ".cia-cs__description",
                    "[data-zone-name='description']",
                    "._13m-c",
                    ".xt_vL"
                ]
                
                for selector in description_selectors:
                    try:
                        description = self.driver.find_element("css selector", selector).text.strip()
                        if description:
                            product_data["общее"]["описание"] = description
                            break
                    except:
                        continue
                    
            except Exception as e:
                logging.warning(f"Ошибка при сборе данных с Яндекс.Маркета: {e}")

            # Сначала собираем изображения с Яндекс.Маркета
            market_img_urls = []
            try:
                market_img_selectors = [
                    "img[srcset*='orig']",
                    "img.cia-cs__image",
                    "img[src*='get-mpic']",
                    "._2ZtSB img",
                    "button[data-autotest-id='thumbnail'] img",
                    ".cia-cs__pictures img",
                    "div[data-zone-name='gallery'] img",
                    "img[data-zone-name='image']",
                    ".V5djR img",  # Селектор галереи изображений
                    "._2v-gv img",  # Селектор превью изображений
                    "img[class*='Image']",  # Общий селектор для изображений
                    "div[data-auto='gallery'] img"  # Селектор галереи
                ]
                
                for selector in market_img_selectors:
                    try:
                        images = self.driver.find_elements("css selector", selector)
                        for img in images:
                            try:
                                # Пробуем получить srcset
                                srcset = img.get_attribute("srcset")
                                if srcset:
                                    # Ищем ссылку с максимальным размером
                                    urls = [url.strip().split(" ")[0] for url in srcset.split(",")]
                                    orig_urls = [url for url in urls if "orig" in url]
                                    if orig_urls:
                                        orig_url = max(orig_urls, key=len)  # Берем самый длинный URL (обычно с наибольшим разрешением)
                                        if orig_url and orig_url not in market_img_urls:
                                            market_img_urls.append(orig_url)
                            
                                # В любом случае пробуем src
                                src = img.get_attribute("src")
                                if src and src.startswith("http"):
                                    # Преобразуем URL для получения оригинального изображения
                                    img_url = src.replace("scale_down//", "")
                                    for size in ["_220x220", "_100x100", "55x70", "60x80", "74x100", "90x120", "180x240"]:
                                        img_url = img_url.replace(size, "orig")
                                    if "get-mpic" in img_url and img_url not in market_img_urls:
                                        market_img_urls.append(img_url)
                            except:
                                continue
                    except:
                        continue
                    
            except Exception as e:
                logging.warning(f"Ошибка при сборе изображений с Яндекс.Маркета: {e}")

            # Затем собираем изображения с Яндекс.Картинок
            images_query = quote(product_name)
            images_url = f"https://yandex.ru/images/search?text={images_query}&type=product"
            
            yandex_img_urls = []
            try:
                self.driver.get(images_url)
                time.sleep(3)
                
                # Оптимизированные селекторы для быстрого поиска
                yandex_img_selectors = [
                    ".ImagesContentImage-Cover",  # Ссылки на изображения
                    ".SerpItem .ImagesContentImage-Cover",  # Ссылки в результатах поиска
                    ".JustifierRowLayout-Item .ImagesContentImage-Cover"  # Ссылки в сетке
                ]
                
                for selector in yandex_img_selectors:
                    if len(yandex_img_urls) >= 5:
                        break
                        
                    try:
                        links = self.driver.find_elements("css selector", selector)
                        for link in links[:10]:  # Ограничиваем количество проверяемых ссылок
                            try:
                                href = link.get_attribute("href")
                                if href and "img_url=" in href:
                                    # Извлекаем URL изображения из параметра img_url
                                    img_url = href.split("img_url=")[1].split("&")[0]
                                    img_url = requests.utils.unquote(img_url)  # Декодируем URL
                                    
                                    # Очищаем URL и пытаемся получить оригинальное изображение
                                    if "https://" not in img_url and "http://" not in img_url:
                                        img_url = "https://" + img_url.lstrip("/")
                                    
                                    # Пытаемся получить оригинальное изображение
                                    if "avatars.mds.yandex.net" in img_url:
                                        img_url = img_url.replace("images-thumbs", "images")
                                        if "&n=13" in img_url:
                                            img_url = img_url.split("&n=13")[0]
                                    
                                    # Заменяем параметры размера на orig
                                    for size in ["size=", "scale_", "_w", "_h", "_size", "preview", "thumb"]:
                                        if size in img_url:
                                            img_url = img_url.split(size)[0] + "orig"
                                    
                                    if img_url not in yandex_img_urls:
                                        yandex_img_urls.append(img_url)
                                        logging.info(f"Найдено изображение на Яндекс.Картинках: {img_url}")
                                        if len(yandex_img_urls) >= 5:
                                            break
                            except Exception as e:
                                logging.debug(f"Ошибка при обработке ссылки: {e}")
                                continue
                    except Exception as e:
                        logging.debug(f"Ошибка при поиске по селектору {selector}: {e}")
                        continue
                        
            except Exception as e:
                logging.warning(f"Ошибка при сборе изображений с Яндекс.Картинок: {e}")

            # Объединяем уникальные изображения из обоих источников
            # Сначала берем до 3 изображений с Маркета
            all_img_urls = []
            for url in market_img_urls[:3]:
                if url not in all_img_urls:
                    all_img_urls.append(url)
            
            # Добавляем до 2 изображений с Яндекс.Картинок
            for url in yandex_img_urls[:2]:
                if url not in all_img_urls:
                    all_img_urls.append(url)

            # Если с Маркета было меньше 3 изображений, добираем из Яндекс.Картинок
            if len(all_img_urls) < 5:
                for url in yandex_img_urls[2:]:
                    if url not in all_img_urls:
                        all_img_urls.append(url)
                        if len(all_img_urls) >= 5:
                            break

            logging.info(f"Собрано изображений с Маркета: {len(market_img_urls)}")
            logging.info(f"Собрано изображений с Яндекс.Картинок: {len(yandex_img_urls)}")
            logging.info(f"Всего уникальных изображений: {len(all_img_urls)}")

            return {
                "название_товара": product_name,
                "характеристики": product_data["характеристики"],
                "функции": product_data["функции"],
                "общее": product_data["общее"],
                "дополнительно": product_data["дополнительно"],
                "изображения": {
                    "маркет": market_img_urls[:3],  # До 3 изображений с Маркета
                    "картинки": yandex_img_urls[:3]  # До 3 изображений с Яндекс.Картинок
                },
                "ссылки": {
                    "товар": product_url,
                    "поиск_маркет": market_url,
                    "поиск_картинки": images_url
                },
                "время_запроса": time.strftime("%Y-%m-%d %H:%M:%S")
            }
            
        except Exception as e:
            logging.error(f"Ошибка при обработке {product_name}: {str(e)}")
            return {
                "error": str(e), 
                "product_name": product_name,
                "ссылка_на_поиск": market_url
            }

    def process_product_list(self, products: List[str], output_file: str = "results.json"):
        results = []
        total_products = len(products)
        total_start_time = time.time()
        
        for index, product in enumerate(products, 1):
            product_start_time = time.time()
            
            result = self.search_product(product)
            results.append(result)
            
            # Сохраняем результаты каждые 5 товаров
            if index % 5 == 0:
                with open(output_file, 'w', encoding='utf-8') as f:
                    json.dump(results, f, ensure_ascii=False, indent=2)
            
            product_time = time.time() - product_start_time
            elapsed_total = time.time() - total_start_time
            estimated_remaining = (elapsed_total / index) * (total_products - index)
            
            logging.info(f"\nТовар [{index}/{total_products}]: {product}")
            logging.info(f"Время обработки: {product_time:.1f} сек")
            logging.info(f"Общее время: {elapsed_total/60:.1f} мин")
            logging.info(f"Осталось примерно: {estimated_remaining/60:.1f} мин")
            logging.info("=" * 50)
            
            if index < total_products:
                time.sleep(2)  # Уменьшаем паузу между запросами
        
        # Сохраняем финальные результаты
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(results, f, ensure_ascii=False, indent=2)
            
        total_time = time.time() - total_start_time
        logging.info(f"\nГотово! Обработано товаров: {total_products}")
        logging.info(f"Общее время: {total_time/60:.1f} мин")
        logging.info(f"Среднее время на товар: {total_time/total_products:.1f} сек")
        return results

    def __del__(self):
        """Закрытие браузера при завершении работы"""
        if hasattr(self, 'driver'):
            self.driver.quit()

def main():
    # Пример использования
    scraper = ProductScraper(model_name="llama2")  # Используем llama2 для лучшего качества
    
    # Загрузка списка продуктов из CSV файла
    try:
        df = pd.read_csv("products.csv", quoting=1)  # csv.QUOTE_ALL
        products = df['product_name'].tolist()
        logging.info(f"Загружено {len(products)} товаров из CSV файла")
    except Exception as e:
        logging.error(f"Ошибка чтения products.csv: {str(e)}")
        products = []
    
    if products:
        results = scraper.process_product_list(products)
        logging.info(f"Обработка завершена. Результаты сохранены в results.json")
    else:
        logging.error("Нет товаров для обработки")

if __name__ == "__main__":
    main() 