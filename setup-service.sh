#!/bin/bash

# Определение пути к директории скрипта, независимо от места запуска
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$SCRIPT_PATH"
BACKEND_DIR="$PROJECT_DIR/backend"
SERVICE_NAME="onlinestore"
SERVICE_FILE="$PROJECT_DIR/$SERVICE_NAME.service"
TEMP_SERVICE_FILE="/tmp/$SERVICE_NAME.service.tmp"

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

# Функция для вывода сообщения об ошибке и выхода
error() {
    echo -e "${RED}Ошибка: $1${NC}" >&2
    exit 1
}

# Проверка запуска с правами администратора
if [ "$EUID" -ne 0 ]; then
    error "Скрипт должен быть запущен с правами администратора (sudo)."
fi

# Функция для установки сервиса
install_service() {
    echo -e "${YELLOW}Установка сервиса $SERVICE_NAME...${NC}"
    echo -e "Используется директория проекта: $PROJECT_DIR"
    echo -e "Директория backend: $BACKEND_DIR"
    
    # Проверка наличия директории backend
    if [ ! -d "$BACKEND_DIR" ]; then
        error "Директория backend не найдена: $BACKEND_DIR"
    fi
    
    # Проверка наличия файла artisan в директории backend
    if [ ! -f "$BACKEND_DIR/artisan" ]; then
        error "Файл artisan не найден в директории backend: $BACKEND_DIR/artisan"
    fi
    
    # Проверка наличия файла сервиса
    if [ ! -f "$SERVICE_FILE" ]; then
        error "Файл сервиса $SERVICE_FILE не найден!"
    fi
    
    # Создаем временный файл и заменяем путь в файле сервиса
    cp "$SERVICE_FILE" "$TEMP_SERVICE_FILE"
    
    # Заменяем путь /OnlineStore на реальный абсолютный путь к директории backend
    sed -i "s|WorkingDirectory=/OnlineStore|WorkingDirectory=$BACKEND_DIR|g" "$TEMP_SERVICE_FILE"
    
    echo -e "Путь в файле сервиса обновлен с /OnlineStore на $BACKEND_DIR"
    
    # Копирование обновленного файла сервиса в директорию systemd
    cp "$TEMP_SERVICE_FILE" "/etc/systemd/system/$SERVICE_NAME.service" || error "Не удалось скопировать файл сервиса."
    
    # Удаляем временный файл
    rm -f "$TEMP_SERVICE_FILE"
    
    # Перезагрузка конфигурации systemd
    systemctl daemon-reload || error "Не удалось перезагрузить конфигурацию systemd."
    
    echo -e "${GREEN}Сервис $SERVICE_NAME успешно установлен.${NC}"
    echo -e "Используйте следующие команды для управления сервисом:"
    echo -e "  ${YELLOW}sudo systemctl start $SERVICE_NAME${NC} - для запуска сервиса"
    echo -e "  ${YELLOW}sudo systemctl stop $SERVICE_NAME${NC} - для остановки сервиса"
    echo -e "  ${YELLOW}sudo systemctl restart $SERVICE_NAME${NC} - для перезапуска сервиса"
    echo -e "  ${YELLOW}sudo systemctl status $SERVICE_NAME${NC} - для проверки статуса сервиса"
    echo -e "  ${YELLOW}sudo systemctl enable $SERVICE_NAME${NC} - для автозапуска при загрузке системы"
    echo -e "  ${YELLOW}sudo systemctl disable $SERVICE_NAME${NC} - для отключения автозапуска"
}

# Функция для запуска сервиса
start_service() {
    echo -e "${YELLOW}Запуск сервиса $SERVICE_NAME...${NC}"
    systemctl start $SERVICE_NAME || error "Не удалось запустить сервис."
    echo -e "${GREEN}Сервис $SERVICE_NAME запущен.${NC}"
    systemctl status $SERVICE_NAME
}

# Функция для остановки сервиса
stop_service() {
    echo -e "${YELLOW}Остановка сервиса $SERVICE_NAME...${NC}"
    systemctl stop $SERVICE_NAME || error "Не удалось остановить сервис."
    echo -e "${GREEN}Сервис $SERVICE_NAME остановлен.${NC}"
}

# Функция для перезапуска сервиса
restart_service() {
    echo -e "${YELLOW}Перезапуск сервиса $SERVICE_NAME...${NC}"
    systemctl restart $SERVICE_NAME || error "Не удалось перезапустить сервис."
    echo -e "${GREEN}Сервис $SERVICE_NAME перезапущен.${NC}"
    systemctl status $SERVICE_NAME
}

# Функция для вывода статуса сервиса
status_service() {
    systemctl status $SERVICE_NAME
}

# Функция для включения автозапуска сервиса
enable_service() {
    echo -e "${YELLOW}Включение автозапуска сервиса $SERVICE_NAME...${NC}"
    systemctl enable $SERVICE_NAME || error "Не удалось включить автозапуск сервиса."
    echo -e "${GREEN}Автозапуск сервиса $SERVICE_NAME включен.${NC}"
}

# Функция для отключения автозапуска сервиса
disable_service() {
    echo -e "${YELLOW}Отключение автозапуска сервиса $SERVICE_NAME...${NC}"
    systemctl disable $SERVICE_NAME || error "Не удалось отключить автозапуск сервиса."
    echo -e "${GREEN}Автозапуск сервиса $SERVICE_NAME отключен.${NC}"
}

# Функция для удаления сервиса
remove_service() {
    echo -e "${YELLOW}Удаление сервиса $SERVICE_NAME...${NC}"
    
    # Остановка сервиса, если запущен
    systemctl stop $SERVICE_NAME 2>/dev/null
    
    # Отключение автозапуска
    systemctl disable $SERVICE_NAME 2>/dev/null
    
    # Удаление файла сервиса
    rm -f /etc/systemd/system/$SERVICE_NAME.service || error "Не удалось удалить файл сервиса."
    
    # Перезагрузка конфигурации systemd
    systemctl daemon-reload || error "Не удалось перезагрузить конфигурацию systemd."
    
    echo -e "${GREEN}Сервис $SERVICE_NAME успешно удален.${NC}"
}

# Вывод справки
show_help() {
    echo "Использование: sudo $0 [команда]"
    echo "Команды:"
    echo "  install - установка сервиса"
    echo "  start - запуск сервиса"
    echo "  stop - остановка сервиса"
    echo "  restart - перезапуск сервиса"
    echo "  status - проверка статуса сервиса"
    echo "  enable - включение автозапуска сервиса"
    echo "  disable - отключение автозапуска сервиса"
    echo "  remove - удаление сервиса"
}

# Обработка аргументов командной строки
case "$1" in
    install)
        install_service
        ;;
    start)
        start_service
        ;;
    stop)
        stop_service
        ;;
    restart)
        restart_service
        ;;
    status)
        status_service
        ;;
    enable)
        enable_service
        ;;
    disable)
        disable_service
        ;;
    remove)
        remove_service
        ;;
    *)
        show_help
        ;;
esac

exit 0 