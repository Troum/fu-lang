#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use FuLang\Core\Parser;
use FuLang\Core\Interpreter;

$interpreter = new Interpreter();
$interpreter->addDefaultFunctions();

$args = [];

echo "🧠 REPL функционального языка" . PHP_EOL;
echo "Введите выражение или команду. Для выхода введите ':exit'." . PHP_EOL;

while (true) {
    echo "> ";
    $line = trim(fgets(STDIN));

    if (in_array(strtolower($line), [':exit', ':quit'], true)) {
        echo "👋 До свидания!" . PHP_EOL;
        break;
    }

    if (str_starts_with($line, ':')) {
        $parts = explode(' ', $line);
        $cmd = strtolower($parts[0]);

        match ($cmd) {
            ':setarg' => (function() use (&$args, $parts) {
                if (!isset($parts[1], $parts[2])) {
                    echo "Использование: :setarg НОМЕР ЗНАЧЕНИЕ" . PHP_EOL;
                    return;
                }
                $index = (int)$parts[1];
                $value = $parts[2];
                $args[$index] = $value;
                echo "Аргумент #$index установлен как '$value'" . PHP_EOL;
            })(),
            ':args' => (function() use ($args) {
                echo "📦 Текущие аргументы:" . PHP_EOL;
                if (empty($args)) {
                    echo "  (пусто)" . PHP_EOL;
                }
                foreach ($args as $i => $val) {
                    echo "  [$i] => " . var_export($val, true) . PHP_EOL;
                }
            })(),
            ':reset' => (function() use (&$args) {
                $args = [];
                echo "Аргументы сброшены." . PHP_EOL;
            })(),
            ':help' => (function() {
                echo <<<HELP
⚙ Доступные команды:
  :setarg N VALUE   Установить аргумент под номером N
  :args             Показать текущие аргументы
  :reset            Сбросить аргументы
  :exit / :quit     Завершить работу
HELP;
                echo PHP_EOL;
            })(),
            default => print("Неизвестная команда: $cmd" . PHP_EOL)
        };

        continue;
    }

    try {
        $interpreter->bindArguments($args);
        $ast = (new Parser($line))->parse();
        $result = $interpreter->eval($ast);

        echo is_scalar($result) || $result === null
            ? $result . PHP_EOL
            : json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    } catch (Throwable $e) {
        echo "Ошибка: " . $e->getMessage() . PHP_EOL;
    }
}