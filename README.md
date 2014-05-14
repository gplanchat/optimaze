# Optimaze, a PHP Tokenizer and Lexer framework

## What is Optimaze?

_Optimaze_ is a PHP framework for source code parsing. It may be used for code reflection, minification, optimization, transformation and/or obfuscation.

It is composed of 4 main components : 
* **Tokenizer** : Parses the source file and transforms the sources into a *token stream*
* **Lexer** : Reads the *token stream* and transforms it into a *lexical tree*
* **CodeManipulator** : Manipulates the *lexical tree* in order to change the code beahvior for optimizations or obfuscation or minification or all those at once
* **Reflection** : Pulls out code structure informations, including classes structures and docblocks

## Planned features

It is planned to manage multiple languages parsing, including :
* _JavaScript_
* _TypeScript_
* _CoffeeScript_
* _Dart_
* _Php_
* _Hack_
* _Zephyr_

At term, it _may_ make possible to translate source code from one language to another related language. This is planned mainly for compatiblity purposes, translations would be such as :
* __Php__ to _Zephyr_ or _Hack_
* __Hack__ to _Zephyr_ or _Php_
* __Zephyr__ to _Php_ or _Hack_
* __JavaScript__ to _Dart_
* __TypeScript__ to _JavaScript_ or _Dart_ or _CoffeeScript_
* __CoffeeScript__ to _JavaScript_ or _Dart_ or _TypeScript_
* __Dart__ to _JavaScript_ or _CoffeeScript_ or _TypeScript_

## Project status

Optimaze is in active development phase. Some components are currently fully operational, others aren't.

| Component                        | Version      |
|:-------------------------------- | ------------ |
| `Tokenizer`                      | _1.0-stable_ |
| `Lexer`                          | _1.0-stable_ |
| `CodeManipulator`                | _1.0-alpha_  |
| `Reflection`                     | _planned_    |
| **JavaScript**                   |              |
| + `JavaScript\Tokenizer  `       | _1.0-stable_ |
| + `JavaScript\Lexer`             | _1.0-beta_   |
| + `JavaScript\CodeManipulator`   | _planned_    |
| + `JavaScript\Reflection`        | _planned_    |
| **Dart**                         |              |
| + `Dart\Tokenizer`               | _wished_     |
| + `Dart\Lexer`                   | _wished_     |
| + `Dart\CodeManipulator`         | _wished_     |
| + `Dart\Reflection`              | _wished_     |
| **TypeScript**                   |              |
| + `TypeScript\Tokenizer`         | _wished_     |
| + `TypeScript\Lexer`             | _wished_     |
| + `TypeScript\CodeManipulator`   | _wished_     |
| + `TypeScript\Reflection`        | _wished_     |
| **CoffeeScript**                 |              |
| + `CoffeeScript\Tokenizer`       | _wished_     |
| + `CoffeeScript\Lexer`           | _wished_     |
| + `CoffeeScript\CodeManipulator` | _wished_     |
| + `CoffeeScript\Reflection`      | _wished_     |
| **PHP**                          |              |
| + `Php\Tokenizer`                | _wished_     |
| + `Php\Lexer`                    | _wished_     |
| + `Php\CodeManipulator`          | _wished_     |
| + `Php\Reflection`               | _wished_     |
| **Zephyr**                       |              |
| + `Zephyr\Tokenizer`             | _wished_     |
| + `Zephyr\Lexer`                 | _wished_     |
| + `Zephyr\CodeManipulator`       | _wished_     |
| + `Zephyr\Reflection`            | _wished_     |
| **Hack**                         |              |
| + `Hack\Tokenizer`               | _wished_     |
| + `Hack\Lexer`                   | _wished_     |
| + `Hack\CodeManipulator`         | _wished_     |
| + `Hack\Reflection`              | _wished_     |

## Build status

[![Build Status](https://travis-ci.org/gplanchat/optimaze.svg?branch=master)](https://travis-ci.org/gplanchat/optimaze)


