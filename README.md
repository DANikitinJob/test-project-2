# REST API

REST API приложение для работы с организациями, зданиями и видами деятельности.

## Установка проекта с помощью Docker

Для установки проекта с помощью Docker выполните следующие шаги:

1. **Клонируйте репозиторий**:
   ```bash
   git clone https://github.com/DANikitinJob/test-project-2.git
   cd test-project-2
   ```

2. **Соберите Docker-образ**:
   ```bash
   docker compose build
   ```

3. **Запустите контейнеры**:
   ```bash
   docker compose up -d
   ```

4. **Обновите Composer**:
    ```bash
    docker compose run composer update
    ```
   
5. **Настройте .env файл**:
    ```bash
    docker compose exec php cp .env.example .env
    ```
    - Измените значения для подключения к БД в .env файле
    - Установите свой ключ в API_KEY (обязательно для работы с API)
    ```bash
    docker compose exec php php artisan key:generate
    ```

6. **Запустите миграции и заполнитее БД тестовыми данными**:
   ```bash
   docker compose exec php php artisan migrate --seed
   docker compose exec php php artisan db:seed
   ```

## API Documentation

API документация доступна через Swagger UI после запуска проекта:

- URL: `http://localhost/api/documentation`
- Все endpoints требуют заголовок `X-API-KEY` для аутентификации
- В Swagger UI нажмите кнопку "Authorize" и введите ваш API ключ для тестирования endpoints

## API Endpoints

| Метод | Endpoint                             | Описание                                  |
| ----- | ------------------------------------ | ----------------------------------------- |
| GET   | /buildings                           | Получить все здания                       |
| GET   | /buildings/{building}/organizations  | Получить организации по зданию            |
| GET   | /activities/{activity}/organizations | Получить организации по виду деятельности |
| GET   | /organizations/geo/search            | Поиск организаций по геолокации           |
| GET   | /organizations/search                | Поиск организаций по названию             |
| GET   | /organizations/activity/{activity}   | Поиск организаций по виду деятельности    |
| GET   | /organizations/{organization}        | Получить информацию об организации по ID  |
| POST  | /activities/store                    | Создать новую активность                  |



