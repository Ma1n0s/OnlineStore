#!/bin/bash

# Определение пути к директории скрипта и backend, независимо от места запуска
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$SCRIPT_PATH/backend"
PID_FILE="$BACKEND_DIR/storage/app/laravel.pid"

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

# Функция для вывода ошибки
error() {
    echo -e "${RED}Ошибка: $1${NC}" >&2
    exit 1
}

# Проверка наличия директории backend
if [ ! -d "$BACKEND_DIR" ]; then
    error "Директория backend не найдена: $BACKEND_DIR"
fi

# Функция запуска приложения
start_app() {
    echo -e "${YELLOW}Запуск Laravel приложения...${NC}"
    echo -e "Используется директория backend: $BACKEND_DIR"
    
    # Проверка, не запущено ли уже приложение
    if [ -f "$PID_FILE" ]; then
        pid=$(cat "$PID_FILE")
        if ps -p $pid > /dev/null 2>&1; then
            echo -e "${RED}Приложение уже запущено (PID: $pid)${NC}"
            return 1
        else
            echo "Предыдущий PID-файл обнаружен, но процесс не найден. Удаляю старый PID-файл."
            rm -f "$PID_FILE"
        fi
    fi
    
    # Проверка существования директории логов
    LOG_DIR="$BACKEND_DIR/storage/logs"
    if [ ! -d "$LOG_DIR" ]; then
        echo "Создание директории для логов: $LOG_DIR"
        mkdir -p "$LOG_DIR" || error "Не удалось создать директорию для логов"
    fi
    
    # Переход в директорию backend
    cd "$BACKEND_DIR" || error "Не удалось перейти в директорию backend"
    
    # Запуск сервера Laravel в фоновом режиме
    nohup php artisan serve --host=0.0.0.0 --port=8000 > "$LOG_DIR/server.log" 2>&1 &
    
    # Сохранение PID процесса
    echo $! > "$PID_FILE"
    echo -e "${GREEN}Laravel приложение запущено на http://localhost:8000 (PID: $!)${NC}"
    echo -e "Логи сервера: $LOG_DIR/server.log"
}

# Функция остановки приложения
stop_app() {
    echo -e "${YELLOW}Остановка Laravel приложения...${NC}"
    
    if [ -f "$PID_FILE" ]; then
        pid=$(cat "$PID_FILE")
        if ps -p $pid > /dev/null 2>&1; then
            kill $pid
            echo -e "${GREEN}Приложение остановлено (PID: $pid)${NC}"
        else
            echo -e "${YELLOW}Процесс с PID $pid не найден${NC}"
        fi
        rm -f "$PID_FILE"
    else
        echo -e "${YELLOW}PID-файл не найден, приложение, возможно, не запущено${NC}"
        
        # Попытка найти и остановить процесс php artisan serve
        pids=$(ps aux | grep "[p]hp artisan serve" | awk '{print $2}')
        if [ -n "$pids" ]; then
            echo -e "${YELLOW}Найдены запущенные процессы php artisan serve. Останавливаю...${NC}"
            for pid in $pids; do
                kill $pid
                echo "Остановлен процесс с PID: $pid"
            done
        fi
    fi
}

# Функция перезапуска приложения
restart_app() {
    stop_app
    sleep 2
    start_app
}

# Функция проверки статуса приложения
status_app() {
    if [ -f "$PID_FILE" ]; then
        pid=$(cat "$PID_FILE")
        if ps -p $pid > /dev/null 2>&1; then
            echo -e "${GREEN}Приложение запущено (PID: $pid)${NC}"
            return 0
        else
            echo -e "${YELLOW}PID-файл существует, но процесс не найден${NC}"
            return 1
        fi
    else
        echo -e "${YELLOW}PID-файл не найден, приложение не запущено${NC}"
        return 1
    fi
}

# Вывод справки
show_help() {
    echo "Использование: $0 [команда]"
    echo "Команды:"
    echo "  start - запуск Laravel приложения"
    echo "  stop - остановка Laravel приложения"
    echo "  restart - перезапуск Laravel приложения"
    echo "  status - проверка статуса Laravel приложения"
}

# Обработка аргументов командной строки
case "$1" in
    start)
        start_app
        ;;
    stop)
        stop_app
        ;;
    restart)
        restart_app
        ;;
    status)
        status_app
        ;;
    *)
        show_help
        ;;
esac

exit 0 