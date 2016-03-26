# go\DB: работа с различными БД из PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/go/db.svg?style=flat-square)](https://packagist.org/packages/go/db)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.3-8892BF.svg?style=flat-square)](https://php.net/)
[![License](https://poser.pugx.org/go/db/license)](LICENSE)

 * Поддерживаемые адаптеры: MySQL, SQLite, PgSQL
 * Документация: https://github.com/vasa-c/go-db/wiki
 * Обсуждение на pyha.ru: http://pyha.ru/forum/topic/554.0
 * Версия 1.x: https://github.com/vasa-c/godb-old
 * Лицензия: MIT (https://github.com/vasa-c/go-db/blob/master/LICENSE)

## Установка

 * Composer: `go/db`
 * Релизы: https://github.com/vasa-c/go-db/tags
 * Репа: git@github.com:vasa-c/go-db.git

## Документация

 * [Идеология и основные принципы](https://github.com/vasa-c/go-db/wiki/intro)
 * [Установка и системные требования](https://github.com/vasa-c/go-db/wiki/install)
 * [Создание подключения к базе](https://github.com/vasa-c/go-db/wiki/create)
 * [Выполнение запроса](https://github.com/vasa-c/go-db/wiki/query)
 * [Шаблон запроса: плейсхолдеры](https://github.com/vasa-c/go-db/wiki/placeholders)
 * [Именованные плейсхолдеры](https://github.com/vasa-c/go-db/wiki/named)
 * [Префикс имён таблиц](https://github.com/vasa-c/go-db/wiki/prefix)
 * [Разбор результата](https://github.com/vasa-c/go-db/wiki/fetch)
 * [Обработка ошибок](https://github.com/vasa-c/go-db/wiki/Exceptions)
 * [Хранилище объектов подключений](https://github.com/vasa-c/go-db/wiki/Storage)
 * [Отладочная информация](https://github.com/vasa-c/go-db/wiki/debug)
 * [Отложенное подключение и закрытие соединения](https://github.com/vasa-c/go-db/wiki/connect)
 * [Клонирование объекта базы](https://github.com/vasa-c/go-db/wiki/clone)
 * [Надстройка над таблицами](https://github.com/vasa-c/go-db/wiki/Table)
 
### Форматы данных 
 * [Формат WHERE](https://github.com/vasa-c/go-db/wiki/where)
 * [Формат COLS](https://github.com/vasa-c/go-db/wiki/cols)
 
### Дополнительно
 
 * [Unit-тесты](https://github.com/vasa-c/go-db/wiki/tests)
 * [Написание адаптеров](https://github.com/vasa-c/go-db/wiki/Adapters)
 * [Расширение goDB](https://github.com/vasa-c/go-db/wiki/Extend)
 * [Compat: совместимость со старыми версиями](https://github.com/vasa-c/go-db/wiki/Compat)

### Поддерживаемые адаптеры

 * [mysql](https://github.com/vasa-c/go-db/wiki/Adapters_mysql)
 * [mysqlold](https://github.com/vasa-c/go-db/wiki/Adapters_mysqlold)
 * [sqlite](https://github.com/vasa-c/go-db/wiki/Adapters_sqlite)
 * [pgsql](https://github.com/vasa-c/go-db/wiki/Adapters_pgsql)

## Отдельное спасибо

 * Dallone aka Алексей Полев, за адаптер PgSQL
