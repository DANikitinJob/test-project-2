## Установка проекта с помощью Docker

Для установки проекта с помощью Docker выполните следующие шаги:

1. **Клонируйте репозиторий**:
   ```bash
   git clone https://github.com/DANikitinJob/test-project-2.git
   cd test-project-2
   ```

2. **Соберите Docker-образ**:
   ```bash
   docker-compose build
   ```

3. **Запустите контейнеры**:
   ```bash
   docker-compose up -d
   ```

4. **Обновите Compsoer**:
    ```bash
    docker-compose run composer update
    ```
   
5. Настройте .env файл
    ```bash
    docker-compose exec php cp .env.example .env
    Измените значения для подключения к БД
    Установите свой ключ в API_KEY
    docker-compose exec php php artisan key:generate
    ```

6. **Запустите миграции**:
   ```bash
   docker-compose exec php php artisan migrate --seed
   ```

7. **Остановка контейнеров**:
   ```bash
   docker-compose down
   ```
   
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



