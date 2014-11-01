# CHANGELOG

## dev-master

## 2.2.2 (01.11.2014)

* Table: accumulate INSERT
* #38: pre-query
* #34: Fixed `?w` for PgSQL (argument of WHERE must be type boolean)
* #31, #32: Fix error messages for PgSQL
* #37: Fixed exception for named data

## 2.2.1 (03.07.2014)

* Checks dependencies for an adapter
* Fixed debug backtrace for global functions call

## 2.2.0 (22.04.2014)

* #7: Fixed iterators
* Small code review
* Comments translated to English
* Unit tests for real databases
* MySQL: parameter "host" is optional ("localhost" by default)
* SQLite: parameter "filename" is optional (in memory database by default)
* Fixed PHP5.4 array syntax (5.3 compatibility)
* Countable for Result

## 2.1.1 (28.02.2014)

* #26: Table: use column map
* #25: ?order: placeholder for ORDER BY statement
* #24: ?col use key: `?col:field`

## 2.1.0 (27.02.2014)

* Compat: compatibility with older versions
* Table::select(): $limit argument `[$offset, $limit]`

## 2.0.4 (26.02.2014)

* ?where and custom operation (`WHERE x>y-5)
* ?set and opertation (`SET x=x+1`)
* Storage: custom main name & $storage->getMainDB()

## 2.0.3 (22.02.2014)

* #20 Table class
* #17 `?where` placeholder for WHERE statement
* #21 `?cols`: empty array or TRUE is `*`
* Composer
* Class Storage is not final
* Refactoring of unit tests

## 2.0.2 (26.01.2014)

* #14 New autoload: Autoloader:register()
* #12 Fix connector for PgSQL
* #13 PSR-2
* #23 Exception indicates the entry point to go\DB
* #15 Refactoring unit-tests

## 2.0.1 beta (24.04.2012)

* [PgSQL adapter](https://github.com/vasa-c/go-db/wiki/Adapters_pgsql)
* Fix negative number: x=x-?i => x=x--1. Use brackets: x=x-(-1)
* Fix `Exceptions\Query`: `getQuery()` & `getError()` returning `NULL`.
* Ref: some Implementation-methods required `connection` (for PgSQL)
* up phpunit to 3.5

## 2.0.0 beta (09.01.2011)

