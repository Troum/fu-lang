#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use FuLang\Core\Parser;
use FuLang\Core\Interpreter;
use FuLang\Exception\EvalException;
use FuLang\Exception\ParseException;

// === Пример программы из тестового задания ===
$code = <<<CODE
(json, 
    (map, 
        (array, "message"), 
        (array, 
            (concat, "Hello, ", 
                (getArg, 0)
            )
        )
    )
)
CODE;

$inputArg = $argv[1] ?? 'Гость';

$parser = new Parser($code);
try {
    $ast = $parser->parse();
    $interpreter = new Interpreter();
    $interpreter->addDefaultFunctions();
    $interpreter->bindArguments([$inputArg]);

    try {
        $result = $interpreter->eval($ast);
        echo $result . PHP_EOL;
    } catch (EvalException $e) {
        echo $e->getMessage() . PHP_EOL;
    }
} catch (ParseException $e) {
    echo $e->getMessage() . PHP_EOL;
}