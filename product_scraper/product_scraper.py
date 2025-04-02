import os
import json
import pandas as pd
from bs4 import BeautifulSoup
import requests
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from typing import Dict, List, Optional, Any, Union, Tuple
import time
import logging
import subprocess
from urllib.parse import quote
import concurrent.futures
import threading
from functools import lru_cache

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
        # Для синхронизации доступа к модели в многопоточном режиме
        self.lock = threading.Lock()
        
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
        
    @lru_cache(maxsize=50)  # Кэширование для повторяющихся запросов
    def get_completion(self, prompt: str) -> str:
        """Получение ответа от локальной модели"""
        with self.lock:  # Защита от одновременного доступа разных потоков
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
    def __init__(self, model_name: str = "llama2", api_url: str = None, api_token: str = None, num_workers: int = 2):
        self.llm = LocalLLM(model_name)
        self.num_workers = num_workers
        self.drivers = {}
        self.driver_locks = {}
        
        # API настройки
        self.api_url = api_url or os.environ.get("API_URL")
        self.api_token = api_token or os.environ.get("API_TOKEN")
        
        # Кэш для категорий
        self._category_cache = {}
        
    def get_driver(self, thread_id=None):
        """Получает драйвер для текущего потока"""
        thread_id = thread_id or threading.get_ident()
        
        if thread_id not in self.drivers:
            logging.info(f"Создаем новый экземпляр драйвера для потока {thread_id}")
            self.setup_selenium(thread_id)
        
        return self.drivers[thread_id], self.driver_locks[thread_id]
        
    def setup_selenium(self, thread_id=None):
        """Настройка Selenium для динамического скрапинга"""
        thread_id = thread_id or threading.get_ident()
        
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
            driver = webdriver.Chrome(options=chrome_options)
            driver.set_page_load_timeout(15)  # Уменьшаем таймаут загрузки страницы
            driver.implicitly_wait(2)  # Уменьшаем время ожидания элементов
            
            driver.execute_cdp_cmd('Network.setUserAgentOverride', {
                "userAgent": 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36'
            })
            driver.execute_script("Object.defineProperty(navigator, 'webdriver', {get: () => undefined})")
            
            self.drivers[thread_id] = driver
            self.driver_locks[thread_id] = threading.Lock()
        except Exception as e:
            logging.error(f"Ошибка при инициализации Chrome: {e}")
            raise

    def search_product(self, product_name: str) -> Dict:
        """
        Собирает информацию о товаре с Яндекс.Маркета и изображения с Яндекс.Картинок
        """
        thread_id = threading.get_ident()
        driver, lock = self.get_driver(thread_id)
        
        with lock:  # Блокировка для безопасного доступа к драйверу
            try:
                market_query = quote(product_name)
                market_url = f"https://market.yandex.ru/search?text={market_query}&cvredirect=2"
                logging.info(f"\nЯндекс.Маркет поиск URL: {market_url}")
                
                driver.execute_cdp_cmd('Network.setExtraHTTPHeaders', {
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
                
                driver.get(market_url)
                time.sleep(1.5)  # Уменьшаем время ожидания
                
                if "Подтвердите, что запросы отправляли вы, а не робот" in driver.page_source:
                    logging.error("Обнаружена капча!")
                    return {
                        "название_товара": product_name,
                        "ошибка": "Сайт требует подтверждение, что вы не робот",
                        "время_запроса": time.strftime("%Y-%m-%d %H:%M:%S"),
                        "ссылки": {
                            "поиск_маркет": market_url,
                            "товар": "",
                            "поиск_картинки": ""
                        }
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
                        product_links = driver.find_elements("css selector", selector)
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
                        "ссылки": {
                            "поиск_маркет": market_url,
                            "товар": "",
                            "поиск_картинки": ""
                        }
                    }
                
                logging.info(f"URL товара: {product_url}")
                
                driver.get(product_url)
                time.sleep(1.5)  # Уменьшаем время ожидания
                
                # Извлекаем хлебные крошки для определения категорий
                breadcrumbs = []
                try:
                    breadcrumb_selectors = [
                        "[data-zone-name='categoryPath'] .ds-text.pN6Aa",
                        "[data-zone-name='categoryPath'] li span",
                        "[itemtype='https://schema.org/BreadcrumbList'] li span[itemprop='name']",
                        ".EQlfk.QbaOB span",
                        "[data-auto='breadcrumbsContainer'] span",
                        ".Qs9El a, ._3Nkam a, ._2qrJF a",
                        "nav[aria-label='Вы здесь'] ol li span"
                    ]
                    
                    for selector in breadcrumb_selectors:
                        try:
                            elements = driver.find_elements("css selector", selector)
                            if elements:
                                breadcrumbs = [el.text.strip() for el in elements if el.text.strip()]
                                if breadcrumbs:
                                    logging.info(f"Найдены хлебные крошки: {' > '.join(breadcrumbs)}")
                                    break
                        except Exception as e:
                            continue
                except Exception as e:
                    logging.warning(f"Ошибка при извлечении хлебных крошек: {e}")
                
                # Нажимаем на кнопку "Все характеристики"
                try:
                    show_all_buttons = driver.find_elements("css selector", "[data-auto='read-more-button'], [data-zone-name='readMoreButton'] button")
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
                    "спецификации": {},
                    "хлебные_крошки": breadcrumbs
                }
                
                try:
                    # Ищем заголовки категорий
                    section_titles = driver.find_elements("css selector", "button._2AXg- .ds-text_lineClamp_1.ds-text_weight_med")
                    
                    # Находим родительские блоки для каждой категории
                    for title in section_titles:
                        try:
                            title_text = title.text.strip()
                            # Пропускаем пустые заголовки
                            if not title_text:
                                continue
                            
                            category_name = title_text.lower()
                            
                            # Находим следующий за заголовком блок с характеристиками
                            category_block = title.find_element("xpath", "../../following-sibling::div[contains(@class, '_2pPrK')]")
                            
                            # Создаем категорию, если её еще нет
                            if category_name not in product_data["спецификации"]:
                                product_data["спецификации"][category_name] = {}
                            
                            # Ищем строки характеристик в блоке категории
                            rows = category_block.find_elements("css selector", "._3rW2x")
                            for row in rows:
                                try:
                                    name_elem = row.find_element("css selector", "._6DaYY")
                                    value_elem = row.find_element("css selector", ".b2ZT4")
                                    
                                    if name_elem and value_elem:
                                        name = name_elem.text.strip()
                                        value = value_elem.text.strip()
                                        
                                        # Пропускаем пустые значения
                                        if not name or not value or name == ":" or value == ":":
                                            continue
                                        
                                        # Добавляем в соответствующую категорию
                                        product_data["спецификации"][category_name][name] = value
                                except Exception as e:
                                    logging.debug(f"Ошибка при обработке строки характеристики: {e}")
                                    continue
                        except Exception as e:
                            logging.debug(f"Ошибка при обработке заголовка категории: {e}")
                            continue
                    
                    # Если категории не найдены, попробуем найти просто все характеристики
                    if not product_data["спецификации"]:
                        # Создаем категорию "характеристики" для всех найденных параметров
                        product_data["спецификации"]["характеристики"] = {}
                        
                        # Парсим все характеристики без категорий
                        spec_rows = driver.find_elements("css selector", "._3rW2x")
                        
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
                                    
                                    # Добавляем все в характеристики
                                    product_data["спецификации"]["характеристики"][name] = value
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
                            description = driver.find_element("css selector", selector).text.strip()
                            if description:
                                # Добавляем описание в отдельное поле
                                product_data["описание"] = description
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
                            images = driver.find_elements("css selector", selector)
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
                    driver.get(images_url)
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
                            links = driver.find_elements("css selector", selector)
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

                # Создаем результирующий словарь с данными о товаре
                result = {
                    "название_товара": product_name,
                    "спецификации": product_data["спецификации"],
                    "описание": product_data.get("описание", ""),
                    "изображения": {
                        "маркет": market_img_urls[:3],  # До 3 изображений с Маркета
                        "картинки": yandex_img_urls[:3]  # До 3 изображений с Яндекс.Картинок
                    },
                    "ссылки": {
                        "товар": product_url,
                        "поиск_маркет": market_url,
                        "поиск_картинки": images_url
                    },
                    "время_запроса": time.strftime("%Y-%m-%d %H:%M:%S"),
                    "хлебные_крошки": product_data.get("хлебные_крошки", [])
                }
                
                # Определяем категорию и подкатегорию
                category, subcategory = self.detect_category_and_subcategory(result)
                result["категория"] = category
                result["подкатегория"] = subcategory
                
                return result
            except Exception as e:
                logging.error(f"Ошибка при обработке товара '{product_name}': {str(e)}")
                return {
                    "название_товара": product_name,
                    "ошибка": f"Ошибка при обработке товара: {str(e)}",
                    "время_запроса": time.strftime("%Y-%m-%d %H:%M:%S"),
                    "ссылки": {
                        "поиск_маркет": market_url if 'market_url' in locals() else "",
                        "товар": product_url if 'product_url' in locals() else "",
                        "поиск_картинки": images_url if 'images_url' in locals() else ""
                    }
                }

    def process_product_list(self, products: List[str], output_file: str = "results.json", save_to_api: bool = False):
        """
        Обрабатывает список товаров и сохраняет результаты
        """
        results = []
        successful_api_saves = 0
        
        with concurrent.futures.ThreadPoolExecutor(max_workers=self.num_workers) as executor:
            # Запускаем обработку товаров в отдельных потоках
            future_to_product = {executor.submit(self.search_product, product): product for product in products}
            
            for future in concurrent.futures.as_completed(future_to_product):
                product_name = future_to_product[future]
                try:
                    product_data = future.result()
                    results.append(product_data)
                    
                    # Сохраняем в API, если нужно
                    if save_to_api:
                        api_result = self.save_to_api(product_data)
                        if api_result.get("status") == "success":
                            successful_api_saves += 1
                            logging.info(f"✓ Товар '{product_name}' успешно сохранен в API (ID: {api_result.get('id')})")
                        else:
                            logging.error(f"✗ Ошибка при сохранении товара '{product_name}' в API: {api_result.get('error', 'Неизвестная ошибка')}")
                    
                    # Сохраняем промежуточные результаты после каждого товара
                    with open(output_file, 'w', encoding='utf-8') as f:
                        json.dump(results, f, ensure_ascii=False, indent=2)
                
                except Exception as e:
                    logging.error(f"Ошибка при обработке '{product_name}': {str(e)}")
        
        logging.info(f"Обработано {len(products)} товаров. Успешно сохранено в API: {successful_api_saves}")
        return results
    
    def save_to_api(self, product_data: Dict[str, Any]) -> Dict[str, Any]:
        """
        Сохраняет данные о товаре через API Laravel
        
        Args:
            product_data: Словарь с данными о товаре
            
        Returns:
            Dict с результатом запроса (id товара или сообщение об ошибке)
        """
        if not self.api_url:
            return {"status": "error", "error": "API URL не указан"}
        
        # Пропускаем, если есть ошибка в данных товара
        if "ошибка" in product_data or "error" in product_data:
            error_msg = product_data.get("ошибка") or product_data.get("error", "Неизвестная ошибка")
            return {"status": "error", "error": f"Данные товара содержат ошибку: {error_msg}"}
        
        try:
            headers = {
                "Content-Type": "application/json",
                "Accept": "application/json"
            }
            
            if self.api_token:
                headers["Authorization"] = f"Bearer {self.api_token}"
            
            # Проверяем обязательные поля
            required_fields = ["название_товара", "спецификации", "ссылки", "изображения"]
            for field in required_fields:
                if field not in product_data:
                    return {"status": "error", "error": f"Отсутствует обязательное поле: {field}"}
                    
            # Логируем категорию и подкатегорию
            category = product_data.get("категория", "Другое")
            subcategory = product_data.get("подкатегория", "Разное")
            logging.info(f"Отправка товара '{product_data['название_товара']}' в API (категория: {category}/{subcategory})")
            
            # Подготовка данных для отправки в API
            api_data = {
                "name": product_data["название_товара"],
                "description": product_data.get("описание", ""),
                "product_url": product_data["ссылки"]["товар"],
                "search_market_url": product_data["ссылки"]["поиск_маркет"],
                "search_images_url": product_data["ссылки"]["поиск_картинки"],
                "specifications": product_data["спецификации"],
                "images": {
                    "market": product_data["изображения"]["маркет"],
                    "yandex": product_data["изображения"]["картинки"]
                },
                "created_at": product_data["время_запроса"],
                "category": category,
                "subcategory": subcategory
            }
            
            # Отправка данных в API с небольшим таймаутом
            logging.info(f"Отправка запроса на {self.api_url}/api/products")
            response = requests.post(
                f"{self.api_url}/api/products",
                headers=headers,
                json=api_data,
                timeout=60  # Увеличиваем таймаут до 60 секунд
            )
            
            # Обработка ответа
            if response.status_code in (200, 201):
                product_id = response.json().get("id")
                logging.info(f"Товар успешно сохранен в API с ID: {product_id}")
                return {"status": "success", "id": product_id}
            else:
                try:
                    error_data = response.json()
                    error_message = error_data.get("message") or error_data.get("error", f"HTTP ошибка {response.status_code}")
                except:
                    error_message = f"HTTP ошибка {response.status_code}"
                    
                logging.error(f"Ошибка при сохранении товара: {error_message}")
                return {"status": "error", "error": error_message, "status_code": response.status_code}
                
        except requests.RequestException as e:
            logging.error(f"Ошибка сетевого запроса: {str(e)}")
            return {"status": "error", "error": f"Ошибка сетевого запроса: {str(e)}"}
        except Exception as e:
            logging.error(f"Неизвестная ошибка при сохранении в API: {str(e)}")
            return {"status": "error", "error": f"Непредвиденная ошибка: {str(e)}"}

    def __del__(self):
        """Закрытие Selenium при уничтожении объекта"""
        for driver in self.drivers.values():
            try:
                driver.quit()
            except:
                pass

    def detect_category_and_subcategory(self, product_data: Dict) -> Tuple[str, str]:
        """
        Определяет категорию и подкатегорию для товара на основе собранных данных,
        используя хлебные крошки Яндекс Маркета и ИИ.
        
        Args:
            product_data: Словарь с данными о товаре
            
        Returns:
            Tuple[str, str]: Категория и подкатегория
        """
        # Проверяем, есть ли товар в кэше
        product_name = product_data["название_товара"]
        if product_name in self._category_cache:
            return self._category_cache[product_name]
        
        # Пробуем определить подкатегорию эвристически из названия товара
        heuristic_subcategory = self._extract_subcategory_from_name(product_name)
        if heuristic_subcategory:
            logging.info(f"Эвристически определена подкатегория для '{product_name}': {heuristic_subcategory}")
        
        # Собираем характеристики товара для всех методов
        specs_text = self._prepare_specs_text(product_data)
        description = product_data.get("описание", "")
        
        # Проверяем, есть ли хлебные крошки
        breadcrumbs = product_data.get("хлебные_крошки", [])
        if breadcrumbs and len(breadcrumbs) > 2:  # Нужно минимум 3 элемента
            # Используем хлебные крошки для определения категории и подкатегории
            logging.info(f"Определяем категорию по хлебным крошкам: {breadcrumbs}")
            
            # Фильтруем хлебные крошки, убирая возможный бренд в конце
            # Бренд обычно короткий, не содержит пробелов и цифр
            relevant_breadcrumbs = breadcrumbs.copy()
            if len(breadcrumbs) > 2:
                last_item = breadcrumbs[-1]
                if len(last_item) < 20 and " " not in last_item and not any(c.isdigit() for c in last_item):
                    # Похоже на бренд, удаляем
                    relevant_breadcrumbs = breadcrumbs[:-1]
                    logging.info(f"Исключаем бренд '{last_item}' из хлебных крошек")
            
            if len(relevant_breadcrumbs) >= 3:
                # Формируем промпт для ИИ с учетом хлебных крошек
                breadcrumbs_str = " > ".join(relevant_breadcrumbs)
                
                # Автоматическое определение категории и подкатегории с предпочтением более специфичных категорий (с конца)
                # Подкатегория - предпоследний или предпредпоследний элемент
                # Категория - на один элемент раньше подкатегории
                subcategory_index = len(relevant_breadcrumbs) - 2  # Предпоследний элемент
                auto_subcategory = relevant_breadcrumbs[subcategory_index]
                
                category_index = max(1, subcategory_index - 1)  # Элемент перед подкатегорией, но не меньше 1
                auto_category = relevant_breadcrumbs[category_index]
                
                # Проверяем, что категория не слишком общая
                if category_index < 2 and len(relevant_breadcrumbs) > 3:
                    # Если категория слишком общая (из начала списка), 
                    # а список длинный, берем категорию из середины
                    alt_category_index = max(2, len(relevant_breadcrumbs) // 2)
                    alt_category = relevant_breadcrumbs[alt_category_index]
                    
                    # Предлагаем альтернативную категорию
                    alt_suggestion = f"Альтернативная категория: {alt_category}"
                else:
                    alt_suggestion = ""
                
                # Добавляем эвристически определенную подкатегорию в промпт, если она есть
                heuristic_suggestion = ""
                if heuristic_subcategory:
                    heuristic_suggestion = f"Эвристически определенная подкатегория: {heuristic_subcategory}"
                
                # Формируем промпт для LLM
                prompt = f"""
                Определи категорию и подкатегорию товара на основе информации.

                Товар: {product_name}
                Описание: {description}
                Хлебные крошки: {breadcrumbs_str}
                Характеристики:
                {specs_text}
                
                Предлагаемые варианты из хлебных крошек:
                Категория: {auto_category}
                Подкатегория: {auto_subcategory}
                {alt_suggestion}
                {heuristic_suggestion}

                ОБРАТИ ВНИМАНИЕ: 
                Часто подкатегория содержится прямо в названии товара. Например:
                - "Перфоратор AEG KH24IE" - подкатегория "Перфораторы"
                - "Шуруповерт Bosch GSR 18V" - подкатегория "Шуруповерты"
                - "Щипцы для зачистки электропроводов GROSS" - подкатегория "Щипцы для зачистки"

                СТРОГИЕ ТРЕБОВАНИЯ:
                1. Ответ ТОЛЬКО на РУССКОМ языке, без английских слов.
                2. Категория должна быть средне-специфичной (не слишком общей).
                3. Подкатегория должна быть конкретным типом товара.
                4. Подкатегорию часто можно выделить из первых слов названия товара.
                5. Хлебные крошки могут не содержать подходящей категории - в этом случае определи её по характеристикам товара.
                6. Ответь ТОЛЬКО в формате JSON: {{"category": "Категория", "subcategory": "Подкатегория"}}
                7. НЕ ВЫВОДИ никаких пояснений до или после JSON, ТОЛЬКО сам JSON!
                8. НЕ ИСПОЛЬЗУЙ английский язык ни при каких условиях!
                9. ПРИМЕР правильного ответа: {{"category": "Электроинструмент", "subcategory": "Перфораторы"}}
                10. НЕ ОБЪЯСНЯЙ свой выбор, только JSON!
                """
                
                # Получаем ответ от LLM
                response = self.llm.get_completion(prompt)
                logging.info(f"Ответ LLM для '{product_name}' на основе хлебных крошек: {response}")
                
                # Пытаемся извлечь категории из ответа
                result = self._extract_categories_from_response(response, product_name)
                if result:
                    return result
            
            # Если мы дошли до этой точки, значит хлебные крошки не помогли
            logging.warning(f"Не удалось определить категории по хлебным крошкам для '{product_name}', используем запасной метод")
        
        # Используем запасной метод определения по характеристикам товара
        return self._detect_category_with_ai(product_data, heuristic_subcategory)
    
    def _prepare_specs_text(self, product_data: Dict) -> str:
        """
        Подготавливает текст характеристик товара для использования в промпте.
        
        Args:
            product_data: Словарь с данными о товаре
            
        Returns:
            str: Форматированный текст характеристик
        """
        specs_text = ""
        if "спецификации" in product_data and product_data["спецификации"]:
            for category_name, category_specs in product_data["спецификации"].items():
                specs_text += f"- {category_name}:\n"
                for spec_name, spec_value in category_specs.items():
                    specs_text += f"  * {spec_name}: {spec_value}\n"
        return specs_text
    
    def _extract_categories_from_response(self, response: str, product_name: str) -> Optional[Tuple[str, str]]:
        """
        Извлекает категорию и подкатегорию из ответа LLM.
        
        Args:
            response: Ответ LLM
            product_name: Название товара для логирования
            
        Returns:
            Optional[Tuple[str, str]]: Категория и подкатегория или None в случае ошибки
        """
        try:
            # Очищаем ответ от возможных кавычек или символов кода
            cleaned_response = response.strip()
            
            # Логируем оригинальный ответ для отладки
            logging.debug(f"Оригинальный ответ LLM: {cleaned_response}")
            
            # Находим JSON в ответе (любой текст между { и })
            json_start = cleaned_response.find('{')
            json_end = cleaned_response.rfind('}') + 1
            
            if json_start != -1 and json_end != -1 and json_end > json_start:
                json_str = cleaned_response[json_start:json_end]
                logging.debug(f"Извлеченный JSON: {json_str}")
                
                # Попытка преобразовать строку в JSON объект
                try:
                    data = json.loads(json_str)
                except json.JSONDecodeError as e:
                    # В случае ошибки декодирования, пробуем исправить некоторые распространенные проблемы
                    logging.warning(f"Ошибка при декодировании JSON: {e}, пробуем исправить")
                    
                    # Заменяем одинарные кавычки на двойные
                    json_str = json_str.replace("'", '"')
                    # Заменяем экранированные двойные кавычки
                    json_str = json_str.replace('\\"', '"')
                    # Убираем экранирующие слеши
                    json_str = json_str.replace('\\', '')
                    
                    try:
                        data = json.loads(json_str)
                    except json.JSONDecodeError:
                        logging.error(f"Не удалось декодировать JSON даже после исправлений: {json_str}")
                        return None
                
                # Получаем категорию и подкатегорию
                category = data.get("category", "")
                subcategory = data.get("subcategory", "")
                
                # Проверка наличия категории и подкатегории
                if not category or not subcategory:
                    logging.warning(f"Неполный ответ LLM для '{product_name}': категория='{category}', подкатегория='{subcategory}'")
                    return None
                
                # Проверяем, что категория и подкатегория на русском языке
                if self._contains_latin_chars(category) or self._contains_latin_chars(subcategory):
                    logging.warning(f"Категории содержат латинские символы: {category}, {subcategory}")
                    return None
                
                # Проверка на слишком общие или слишком специфичные категории
                if not self._validate_categories(category, subcategory):
                    logging.warning(f"Некорректные категории: категория='{category}', подкатегория='{subcategory}'")
                    return None
                
                # Нормализуем регистр (первая буква заглавная)
                category = category.strip().title()
                subcategory = subcategory.strip().title()
                
                # Сохраняем в кэш и возвращаем результат
                self._category_cache[product_name] = (category, subcategory)
                logging.info(f"Определены категории для '{product_name}': категория='{category}', подкатегория='{subcategory}'")
                return category, subcategory
            else:
                logging.warning(f"JSON не найден в ответе LLM: {cleaned_response}")
                return None
                
        except Exception as e:
            logging.error(f"Ошибка при обработке ответа LLM: {e}")
            return None
    
    def _validate_categories(self, category: str, subcategory: str) -> bool:
        """
        Проверяет корректность категорий.
        
        Args:
            category: Категория для проверки
            subcategory: Подкатегория для проверки
            
        Returns:
            bool: True, если категории корректны, иначе False
        """
        # Проверка на пустые значения
        if not category or not subcategory:
            return False
            
        # Проверка на слишком общие категории
        too_general = ["товары", "товар", "все товары", "товары для дома", "товары для ремонта", 
                      "разное", "другое", "прочее", "разные товары", "разные", "инструменты",
                      "техника", "электроника", "аксессуары"]
        
        # Проверка на слишком длинные категории (скорее всего это описания)
        if len(category) > 30 or len(subcategory) > 30:
            return False
        
        # Проверка на слишком короткие категории
        if len(category) < 3 or len(subcategory) < 3:
            return False
            
        # Проверка на слишком общие категории
        if category.lower() in too_general:
            return False
        
        # Проверка на идентичность категории и подкатегории
        if category.lower() == subcategory.lower():
            return False
            
        # Проверка на английские слова в категориях
        english_words = ["tool", "tools", "electronic", "electronics", "device", "devices", 
                        "equipment", "gear", "hardware", "home", "kitchen", "bathroom",
                        "garden", "outdoor", "indoor", "power", "hand", "soldering"]
        
        # Проверка на наличие английских слов
        for word in english_words:
            if word.lower() in category.lower() or word.lower() in subcategory.lower():
                return False
        
        return True
    
    def _contains_latin_chars(self, text: str) -> bool:
        """
        Проверяет наличие латинских символов в тексте.
        
        Args:
            text: Строка для проверки
            
        Returns:
            bool: True, если текст содержит латинские символы, иначе False
        """
        latin_chars = set('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
        return any(char in latin_chars for char in text)
            
    def _detect_category_with_ai(self, product_data: Dict, heuristic_subcategory: Optional[str] = None) -> Tuple[str, str]:
        """
        Запасной метод определения категории и подкатегории с помощью ИИ без учета хлебных крошек.
        
        Args:
            product_data: Словарь с данными о товаре
            heuristic_subcategory: Эвристически определенная подкатегория (если есть)
            
        Returns:
            Tuple[str, str]: Категория и подкатегория
        """
        product_name = product_data["название_товара"]
        description = product_data.get("описание", "")
        specs_text = self._prepare_specs_text(product_data)
        
        # Добавляем информацию о эвристически определенной подкатегории
        heuristic_suggestion = ""
        if heuristic_subcategory:
            heuristic_suggestion = f"\nЭвристически определенная подкатегория: {heuristic_subcategory}"
        
        # Формируем промпт для LLM
        prompt = f"""
        Определи категорию и подкатегорию для товара на основе его характеристик.

        Товар: {product_name}
        Описание: {description}
        Характеристики:
        {specs_text}{heuristic_suggestion}
        
        ОБРАТИ ВНИМАНИЕ: 
        Часто подкатегория содержится прямо в названии товара. Например:
        - "Перфоратор AEG KH24IE" - подкатегория "Перфораторы"
        - "Шуруповерт Bosch GSR 18V" - подкатегория "Шуруповерты"
        - "Щипцы для зачистки электропроводов GROSS" - подкатегория "Щипцы для зачистки"

        СТРОГИЕ ТРЕБОВАНИЯ:
        1. Ответ ТОЛЬКО на РУССКОМ ЯЗЫКЕ, без английских слов.
        2. Категория - общая группа товаров (Электроинструмент, Сантехника и т.д.).
        3. Подкатегория - конкретный тип товара внутри категории.
        4. Подкатегорию часто можно выделить из первых слов названия товара.
        5. Категории должны быть на РУССКОМ языке, например "Электроинструмент", а НЕ "Power Tools".
        6. Используй краткие названия (1-2 слова).
        7. Ответь ТОЛЬКО в формате JSON: {{"category": "Категория", "subcategory": "Подкатегория"}}
        8. НЕ ВЫВОДИ никаких пояснений до или после JSON, ТОЛЬКО сам JSON!
        9. ПРИМЕР правильного ответа: {{"category": "Электроинструмент", "subcategory": "Перфораторы"}}
        10. НЕ ОБЪЯСНЯЙ свой выбор, только JSON!
        11. ТОЛЬКО JSON и НИЧЕГО БОЛЬШЕ!
        """
        
        # Получаем ответ от LLM
        response = self.llm.get_completion(prompt)
        logging.info(f"Ответ LLM для '{product_name}' методом запаса: {response}")
        
        # Пытаемся извлечь категории из ответа
        result = self._extract_categories_from_response(response, product_name)
        if result:
            return result
            
        # Если первая попытка не удалась, пробуем еще раз с более строгими инструкциями
        logging.warning(f"Неудачная первая попытка определения категорий для '{product_name}', пробуем еще раз")
        second_prompt = f"""
        Определи категорию и подкатегорию для товара: {product_name}
        
        Подсказка: подкатегория часто содержится в НАЧАЛЕ названия товара.
        {heuristic_suggestion}
        
        ТОЛЬКО НА РУССКОМ ЯЗЫКЕ!
        
        СТРОГИЕ ТРЕБОВАНИЯ:
        1. Ответь ТОЛЬКО в формате JSON: {{"category": "Категория", "subcategory": "Подкатегория"}}
        2. Ответ ОБЯЗАТЕЛЬНО на РУССКОМ языке, без английских слов!
        3. Без пояснений и дополнительного текста.
        4. Например: {{"category": "Электроинструмент", "subcategory": "Перфораторы"}}
        5. НЕ ВЫВОДИ никаких пояснений или объяснений!
        6. ВЫВЕДИ ТОЛЬКО JSON и НИЧЕГО БОЛЬШЕ!
        7. ПРИМЕР правильного ответа: {{"category": "Ручной инструмент", "subcategory": "Шуруповерты"}}
        8. НЕПРАВИЛЬНО: "Я проанализировал товар и..." - не нужны вступления!
        9. ПРАВИЛЬНО: {{"category": "Электроника", "subcategory": "Смартфоны"}}
        """
        
        response = self.llm.get_completion(second_prompt)
        logging.info(f"Повторный ответ LLM для '{product_name}': {response}")
        
        # Пытаемся извлечь категории из повторного ответа
        result = self._extract_categories_from_response(response, product_name)
        if result:
            return result
        
        # Если все попытки не удались, возвращаем значения по умолчанию
        logging.warning(f"Не удалось определить категории для '{product_name}', используем значения по умолчанию")
        return "Другое", "Разное"

    def _extract_subcategory_from_name(self, product_name: str) -> Optional[str]:
        """
        Пытается эвристически определить подкатегорию из названия товара.
        
        Args:
            product_name: Название товара
            
        Returns:
            Optional[str]: Предполагаемая подкатегория или None
        """
        # Словарь инструментов и техники, часто встречающихся в названиях товаров
        # Ключ - слово в начале названия, значение - подкатегория
        common_tools = {
            "перфоратор": "Перфораторы",
            "шуруповерт": "Шуруповерты",
            "дрель": "Дрели",
            "пила": "Пилы",
            "лобзик": "Лобзики",
            "фрезер": "Фрезеры",
            "шлифмашин": "Шлифовальные машины",
            "болгарка": "Угловые шлифмашины",
            "триммер": "Триммеры",
            "плиткорез": "Плиткорезы",
            "отвертка": "Отвертки",
            "ключ": "Ключи",
            "набор": "Наборы инструментов",
            "молоток": "Молотки",
            "бормашина": "Бормашины",
            "гравер": "Граверы",
            "рубанок": "Рубанки",
            "щипцы": "Щипцы",
            "щипцы для зачистки": "Щипцы для зачистки",
            "щипцы для электропроводов": "Щипцы для зачистки",
            "клещи": "Клещи",
            "плоскогубцы": "Плоскогубцы",
            "ножницы": "Ножницы",
            "пистолет": "Строительные пистолеты",
            "степлер": "Степлеры",
            "нож": "Ножи",
            "стремянка": "Стремянки",
            "лестница": "Лестницы",
            "уровень": "Уровни",
            "рулетка": "Рулетки",
            "лазерный": "Лазерные инструменты",
            "сварочный": "Сварочное оборудование",
            "аппарат": "Аппараты",
            "станок": "Станки",
            "компрессор": "Компрессоры",
            "пылесос": "Пылесосы",
            "мойка": "Мойки высокого давления",
            "стиральная": "Стиральные машины",
            "холодильник": "Холодильники",
            "телевизор": "Телевизоры",
            "смартфон": "Смартфоны",
            "планшет": "Планшеты",
            "ноутбук": "Ноутбуки",
            "компьютер": "Компьютеры",
            "принтер": "Принтеры",
            "сканер": "Сканеры",
            "фотоаппарат": "Фотоаппараты",
            "видеокамера": "Видеокамеры"
        }
        
        # Выделить первое слово из названия товара (до первого пробела или цифры)
        name_lower = product_name.lower()
        
        # Специальные случаи для предлогов "для", "по", "с" и т.д.
        prepositions = ["для", "по", "с", "под", "от", "к"]
        
        for preposition in prepositions:
            preposition_with_space = f" {preposition} "
            if preposition_with_space in name_lower:
                parts = name_lower.split(preposition_with_space, 1)
                if len(parts) > 1:
                    first_part = parts[0].strip()
                    second_part = parts[1].strip().split()[0] if parts[1].strip() else ""
                    
                    # Проверяем сначала полное совпадение с словарем
                    combined_key = f"{first_part} {preposition} {second_part}"
                    if combined_key in common_tools:
                        return common_tools[combined_key]
                    
                    # Для случая "щипцы для зачистки электропроводов"
                    if first_part == "щипцы" and second_part == "зачистки":
                        return "Щипцы для зачистки"
                    
                    # Пытаемся составить подкатегорию из первой части + предлог + второй части
                    if first_part and second_part:
                        # Преобразуем в именительный падеж множественного числа
                        first_part_plural = first_part
                        if first_part_plural.endswith("щи"):
                            first_part_plural = first_part_plural[:-1] + "цы"
                        elif first_part_plural.endswith("ок"):
                            first_part_plural = first_part_plural[:-2] + "ки"
                        elif not first_part_plural.endswith(("и", "ы")):
                            first_part_plural += "ы"
                        
                        return f"{first_part_plural.title()} {preposition} {second_part}"
        
        # Специальная проверка для "щипцы для зачистки" с любыми вариациями
        if "щипцы" in name_lower and "зачистк" in name_lower:
            return "Щипцы для зачистки"
        
        # Выделяем часть до первой цифры или служебного символа
        first_word_end = len(name_lower)
        for i, char in enumerate(name_lower):
            if char.isdigit() or char in ",.;:()[]{}«»":
                first_word_end = i
                break
        
        # Получаем первое слово и проверяем его в словаре
        first_part = name_lower[:first_word_end].strip()
        
        # Проверяем на полное совпадение с ключом
        for key, value in common_tools.items():
            if first_part == key:
                return value
        
        # Проверяем на вхождение ключа в начало названия
        for key, value in common_tools.items():
            if first_part.startswith(key):
                return value
            if name_lower.startswith(key):
                return value
        
        # Не удалось определить подкатегорию
        return None

def main():
    # Настройки API
    api_url = os.environ.get("API_URL", "http://localhost:8000")
    api_token = os.environ.get("API_TOKEN", "")
    
    # Определяем количество параллельных потоков
    num_workers = int(os.environ.get("NUM_WORKERS", 2))
    
    # Создаем экземпляр парсера
    parser = ProductScraper(api_url=api_url, api_token=api_token, num_workers=num_workers)
    
    # Флаг сохранения в API (по умолчанию True)
    save_to_api = True
    
    # Попытка загрузки продуктов из CSV файла
    products = []
    try:
        # Формируем абсолютный путь к файлу
        script_dir = os.path.dirname(os.path.abspath(__file__))
        csv_file = os.path.join(script_dir, 'products.csv')
        
        logging.info(f"Пытаемся загрузить файл из: {csv_file}")
        products_df = pd.read_csv(csv_file)
        products = products_df['product_name'].tolist()
        logging.info(f"Загружено {len(products)} товаров из {csv_file}")
    except Exception as e:
        logging.warning(f"Не удалось загрузить товары из CSV: {str(e)}")
        # Тестовый список товаров
        products = [
            "Перфоратор AEG KH24IE 4935451555",
            "Шуруповерт Bosch GSR 18V-60 C Professional",
            "Дрель ударная Metabo SBE 650"
        ]
        logging.info(f"Используем тестовый список из {len(products)} товаров")
    
    # Измеряем время выполнения
    start_time = time.time()
    
    # Обрабатываем список товаров и сохраняем результаты
    results = parser.process_product_list(products, save_to_api=save_to_api)
    
    # Вычисляем общее время
    total_time = time.time() - start_time
    
    # Печатаем краткий отчет
    successful = len([r for r in results if "ошибка" not in r])
    logging.info(f"Итоги: успешно обработано {successful}/{len(results)} товаров за {total_time:.1f} сек")
    logging.info(f"Среднее время на товар: {total_time/len(results):.1f} сек")
    
    if save_to_api:
        api_successful = len([r for r in results if r.get("api_status") == "success"])
        logging.info(f"Успешно сохранено в API: {api_successful}/{len(results)} товаров")

if __name__ == "__main__":
    main() 