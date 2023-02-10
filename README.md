# Парсер CSV файлов по тестовой задаче

## Задача

* [Текст задачи](task/readme.txt)
* [Пример входного файла](task/input.csv)
* [Пример результата](task/output.json)

## Описание установки и работы

Установить git, клонировать проект

`git clone git@github.com:kadartalek/test7.git`

Перейти в папку проекта `cd test7`

Установить `php8.1`

Установить `composer` как приложение или в корень проекта

Установить зависимости

`php8.1 composer.phar install`

или (в зависимости от выбора)

`composer install`

выполнить команду

`bin/console task/input.csv output.json`

или (в зависимости от установки PHP)

`php8.1 bin/console task/input.csv output.json`

## Комментарии по решению задачи

Для начала я использовал консольный компонент symfony в виде single-action application. Однако, для расширения количества комманд, стандартный компонент требует ручную регистрацию каждой команды в ["исполняемом файле"](bin/console), что в будущем может создать конфликты при совместной разработке. Поэтому я написал автоматический загрузчик комманд из определённого неймспейса при помощи конфигурации psr4 в [composer.json](composer.json). Он находится по адресу [https://github.com/kadanin/symfony-psr4-command-loader](https://github.com/kadanin/symfony-psr4-command-loader) или [https://packagist.org/packages/kadanin/symfony-psr4-command-loader](https://packagist.org/packages/kadanin/symfony-psr4-command-loader).
