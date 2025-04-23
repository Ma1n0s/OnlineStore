# Запуск приложения OnlineStore через systemd

В этой директории есть два скрипта для запуска Laravel-приложения:

1. `setup-service.sh` - Скрипт для установки и управления systemd-сервисом (требует sudo)
2. `run-app.sh` - Скрипт для локального запуска приложения (не требует sudo)

## Особенности скриптов

- Скрипты автоматически определяют свое расположение, поэтому вы можете запускать их из любой директории.
- Путь к проекту и директории backend определяется автоматически.
- **Важно**: При установке сервиса происходит автоматическая замена базового пути `/OnlineStore` в файле сервиса на абсолютный путь к директории backend.
- Скрипт установки проверяет наличие директории backend и файла artisan перед установкой.

## Структура файлов

- `onlinestore.service` - Шаблон файла конфигурации systemd с базовым путем `/OnlineStore`.
- `setup-service.sh` - Скрипт, который заменяет базовый путь на реальный при установке сервиса.
- `run-app.sh` - Скрипт для запуска приложения без systemd.

## Как это работает

1. В файле `onlinestore.service` указан базовый путь `WorkingDirectory=/OnlineStore`
2. При установке сервиса скрипт `setup-service.sh` автоматически определяет:
   - Директорию, в которой находится скрипт: `$PROJECT_DIR`
   - Директорию backend: `$BACKEND_DIR` = `$PROJECT_DIR/backend`
3. Затем скрипт заменяет `/OnlineStore` на полный путь к директории backend
4. В результате сервис всегда знает правильный путь к приложению, независимо от расположения директории

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
2. Логи сервера находятся в `backend/storage/logs/server.log`
3. При использовании скрипта `run-app.sh` PID процесса сохраняется в `backend/storage/app/laravel.pid`

## Настройка

Если вы хотите изменить настройки запуска (например, порт или хост), отредактируйте:

1. Для systemd: файл `onlinestore.service` перед установкой
2. Для локального запуска: файл `run-app.sh`

### Настройка пользователя

По умолчанию сервис настроен на запуск от пользователя `root`. Если вы хотите изменить пользователя, отредактируйте параметры `User` и `Group` в файле `onlinestore.service`.

```bash
[Service]
User=другой_пользователь
Group=другая_группа
```

Убедитесь, что указанный пользователь существует в системе и имеет права на доступ к директории проекта.

### Переустановка сервиса

После изменения настроек в `onlinestore.service` необходимо переустановить сервис:

```bash
sudo ./setup-service.sh remove
sudo ./setup-service.sh install
```

## Перемещение директории

Если вы переместили директорию `OnlineStore` в другое место, просто выполните переустановку сервиса:

```bash
sudo ./setup-service.sh remove
sudo ./setup-service.sh install
```

Новый путь будет автоматически обнаружен и применен.
