<?php

namespace FuLang\Core;

use FuLang\Exception\EvalException;

class Interpreter {
    private array $functions = [];
    private array $args = [];

    /**
     * @param string $name
     * @param callable $callback
     * @return void
     */
    public function addFunction(string $name, callable $callback): void {
        $this->functions[$name] = $callback;
    }

    /**
     * @return void
     */
    public function addDefaultFunctions(): void {
        $this->addFunction('array', fn(...$items) => $items);
        $this->addFunction('map', fn($k, $v) => is_array($k) && is_array($v) ? array_combine($k, $v) : throw new EvalException("Функция map() ожидает два массива" . PHP_EOL));
        $this->addFunction('concat', fn($a, $b) => (string)$a . (string)$b);
        $this->addFunction('json', fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE));
        $this->addFunction('upper', fn($v) => mb_strtoupper((string)$v));
        $this->addFunction('lower', fn($v) => mb_strtolower((string)$v));
    }

    /**
     * @param array $args
     * @return void
     */
    public function bindArguments(array $args): void {
        $this->args = $args;
        $this->addFunction('getArg', fn($i) =>
            $args[$i] ?? throw new EvalException("Аргумент с индексом $i не установлен" . PHP_EOL)
        );
    }

    /**
     * @throws EvalException
     */
    public function eval($ast): float|bool|int|string|null
    {
        return $this->evalExpr($ast);
    }

    /**
     * @throws EvalException
     */
    private function evalExpr($expr): mixed
    {
        if (is_scalar($expr) || $expr === null) return $expr;

        if (is_array($expr) && $expr[0] === 'call') {
            [$_, $name, $args] = $expr;
            if (!isset($this->functions[$name])) {
                throw new EvalException("Функция '$name' не определена" . PHP_EOL);
            }
            $evaluated = array_map(fn($e) => $this->evalExpr($e), $args);
            return ($this->functions[$name])(...$evaluated);
        }

        throw new EvalException("Некорректное выражение для вычисления" . PHP_EOL);
    }
}