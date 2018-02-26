# CHANGELOG

## 2.2.13 (17.01.2018)

* PR#66: ?hex for hexadecimal string
* PR#66: ?string now modifier
* #63: WHERE list and NULL

## 2.2.12 (?)

## 2.2.11 (4.07.2016)

* #60: Use prefix for fake db 

## 2.2.10 (7.06.2016)

* #59: Fixed: not specified message and code for the Connect exception

## 2.2.9 (25.04.2016)

* Fix escape identifier names
* Add support for chain and alias 'as' in ?t placeholder

## 2.2.8 (28.03.2016)

* Extended ?col and `NOW()` (a function without arguments)
* #54: Use `DataInvalidFormat`

## 2.2.7 (27.03.2016)

* #44: Extended ?col & ?cols format (https://github.com/vasa-c/go-db/wiki/cols)
* #55: Extended ?table format (https://github.com/vasa-c/go-db/wiki/table-format)
* #46: Extended ?set format (https://github.com/vasa-c/go-db/wiki/set)
* #45: Extended ?where format (https://github.com/vasa-c/go-db/wiki/where)
* Fixed ?order (if scalar it does not add "ASC")
* Added to travis-ci.org
* ConfigError: normal message

## 2.2.6 (12.02.2016)

* FakeTable: some fixes and refactoring.

## 2.2.5 (11.02.2016)

* FakeDB
* FakeTable transactions
* FakeTable log
* Some fixes

## 2.2.4 (10.02.2016)

* FakeTable for tests

## 2.2.3 (18.02.2015)

* Some refactoring and phpdoc fixes

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

