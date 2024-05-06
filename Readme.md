# Документация:

## Необходимые os/утилиты/пакеты:
1) docker-compose-v2

## Запуск приложения:
1) sudo make dc_up  
// запуск приложения (будет доступно по localhost:888) (phpmyadmin доступно по localhost:8090 - заходим с логином root и паролем root)

3) sudo make dc_down  
// завершение работы приложения

5) sudo make app_bash  
// запуск bash докер контейнера

*** дальнейшие действия совершаются в bash докер контейнера ***  
  
4. composer install  
// устанавливаем зависимости 

5) php bin/console doctrine:database:create  
// создаем базу

7) php bin/console doctrine:migrations:migrate  
// применяем миграции

9) php bin/console doctrine:fixtures:load  
// заполняем таблицы тестовыми данными

## Работа с API:
1. Получение всех книг (помимо полей книги, возвращать фамилию автора и наименование издательства)  
   curl --location --request GET 'localhost:888/api/books'

2. Создание нового автора  
   curl --location --request POST 'localhost:888/api/author/create' \
   --header 'Content-Type: application/x-www-form-urlencoded' \
   --data-urlencode 'family_name=Pavlenko' \
   --data-urlencode 'first_name=Sam'

Remarks: Если в базе данных уже есть автор с такими фамилией и именем, то вылетит иключение: "Author with those names are already exists in database!".

3. Создание книги с привязкой к существующему автору  
   curl --location --request POST 'localhost:888/api/book/create' \
   --header 'Content-Type: application/x-www-form-urlencoded' \
   --data-urlencode 'title=Some book 2' \
   --data-urlencode 'publish_year=2024' \
   --data-urlencode 'publisher_id=1' \
   --data-urlencode 'author_id=1'

Remarks: Если в базе данных уже есть книга с такими названием, то вылетит иключение: "Book with this name is already exists in database!".  
         Если в базе данных нет издателя с таким id, то вылетит иключение: "Publisher with id {$request->request->get('publisher_id')} was not found in database".  
         Если в базе данных нет автора с таким id, то вылетит иключение: "Author with id {$request->request->get('author_id')} was not found in database".

4. Редактирование издателя  
   curl --location --request PUT 'localhost:888/api/publisher/update/1' \
   --header 'Content-Type: application/x-www-form-urlencoded' \
   --data-urlencode 'books_ids=2,4,6' \
   --data-urlencode 'publisher_name=New name'

Remarks: Если в базе данных нет издателя с таким id, то вылетит иключение: "Publisher with id {$request->request->get('publisher_id')} was not found in database".  
         Если в базе данных нет книги с таким id, то вылетит иключение: "Book with id {$books_id} was not found in database".

5. Удаление книги/автора/издателя  

5.1. Удаление книги (Soft Delete)  
   curl --location --request DELETE 'localhost:888/api/book/delete/2'
5.2. Удаление автора (Soft Delete)  
   curl --location --request DELETE 'localhost:888/api/author/delete/1'
5.3. Удаление издателя (Soft Delete)  
   curl --location --request DELETE 'localhost:888/api/publisher/delete/1'

## Symfony команды:
1. Команда по наполнению БД тестовыми данными (несколько авторов/книг/издательств)  
   php bin/console doctrine:fixtures:load

2. Команда по удалению всех авторов, у которых нет книг  
*** Не смог реализовать консольную команду без нативных запросов SQL запросов к базе данных. Не смог прокинуть ManagerRegistry объектов в новую консольную команду. Нужна помощь старших коллег 
