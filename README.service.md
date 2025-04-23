# Запуск приложения OnlineStore через systemd

В этой директории есть два скрипта для запуска Laravel-приложения:

1. `setup-service.sh` - Скрипт для установки и управления systemd-сервисом (требует sudo)
2. `run-app.sh` - Скрипт для локального запуска приложения (не требует sudo)

## Использование systemd-сервиса

Systemd-сервис позволяет автоматически запускать приложение при старте системы и управлять его жизненным циклом.

### Установка сервиса

```bash
sudo ./setup-service.sh install
```

### Запуск сервиса

```bash
sudo ./setup-service.sh start
# или
sudo systemctl start onlinestore
```

### Остановка сервиса

```bash
sudo ./setup-service.sh stop
# или
sudo systemctl stop onlinestore
```

### Перезапуск сервиса

```bash
sudo ./setup-service.sh restart
# или
sudo systemctl restart onlinestore
```

### Проверка статуса сервиса

```bash
sudo ./setup-service.sh status
# или
sudo systemctl status onlinestore
```

### Включение автозапуска

```bash
sudo ./setup-service.sh enable
# или
sudo systemctl enable onlinestore
```

### Отключение автозапуска

```bash
sudo ./setup-service.sh disable
# или
sudo systemctl disable onlinestore
```

### Удаление сервиса

```bash
sudo ./setup-service.sh remove
```

## Локальный запуск без systemd

Для разработки и тестирования можно использовать скрипт `run-app.sh`, который запускает Laravel-приложение без использования systemd.

### Запуск приложения

```bash
./run-app.sh start
```

### Остановка приложения

```bash
./run-app.sh stop
```

### Перезапуск приложения

```bash
./run-app.sh restart
```

### Проверка статуса приложения

```bash
./run-app.sh status
```

## Примечания

1. Приложение запускается на порту 8000 и доступно по адресу http://localhost:8000
2. Логи сервера находятся в `/home/frog/Desktop/OnlineStore/backend/storage/logs/server.log`
3. При использовании скрипта `run-app.sh` PID процесса сохраняется в `/home/frog/Desktop/OnlineStore/backend/storage/app/laravel.pid`

## Настройка

Если вы хотите изменить настройки запуска (например, порт или хост), отредактируйте:

1. Для systemd: файл `/home/frog/Desktop/OnlineStore/onlinestore.service`
2. Для локального запуска: файл `/home/frog/Desktop/OnlineStore/run-app.sh`

После изменения настроек в `onlinestore.service` необходимо переустановить сервис:

```bash
sudo ./setup-service.sh remove
sudo ./setup-service.sh install
```
