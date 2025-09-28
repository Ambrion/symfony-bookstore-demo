# Демо сайт "Книжный каталог" на symfony 

* [Описание тестового задания](TASK_README.md)
* [Описание результата](RESULT_README.md)

## Установка

1. Клонируем проект
2. Выполняем `cd .docker && make build && make start && make install`
3. Добавляем в hosts домен для сайта `127.0.0.1 bookstore-symfony.local`
4. Запускаем миграции `make migrate`
5. Запускаем импорт `make import`

`make start` и `make stop` можно использовать для запуска и остановки контейнеров  
`make enter` - зайти к php контейнер
