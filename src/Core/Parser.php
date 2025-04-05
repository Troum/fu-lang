<?php
namespace FuLang\Core;

use FuLang\Exception\ParseException;

class Parser {
    private string $input;
    private int $pos = 0;

    public function __construct(string $input) {
        $this->input = trim($input);
    }

    /**
     * @throws ParseException
     */
    public function parse(): mixed {
        $result = $this->parseExpression();
        $this->skipWhitespace();
        if ($this->pos < strlen($this->input)) {
            throw new ParseException("Ошибка синтаксиса: неожиданные данные после конца выражения" . PHP_EOL);
        }
        return $result;
    }

    /**
     * @throws ParseException
     */
    private function parseExpression(): mixed {
        $this->skipWhitespace();
        $char = $this->peek();
        return match (true) {
            $char === '(' => $this->parseFunctionCall(),
            $char === '"' => $this->parseString(),
            ctype_digit($char) || $char === '-' => $this->parseNumber(),
            default => $this->parseConstant(),
        };
    }

    /**
     * @throws ParseException
     */
    private function parseFunctionCall(): array {
        $this->consume('(');
        $this->skipWhitespace();
        $name = $this->parseIdentifier();
        $args = [];

        $this->skipWhitespace();
        if ($this->peek() === ',') {
            $this->consume(',');
            $this->skipWhitespace();
            $args[] = $this->parseExpression();
            $this->skipWhitespace();
            while ($this->peek() === ',') {
                $this->consume(',');
                $this->skipWhitespace();
                $args[] = $this->parseExpression();
                $this->skipWhitespace();
            }
        }

        $this->consume(')');
        return ['call', $name, $args];
    }

    /**
     * @throws ParseException
     */
    private function parseIdentifier(): string {
        if (!preg_match('/[a-zA-Z_][a-zA-Z0-9_]*/A', $this->input, $matches, 0, $this->pos)) {
            throw new ParseException("Ожидалось имя функции или идентификатор (позиция: $this->pos)" . PHP_EOL);
        }
        $this->pos += strlen($matches[0]);
        return $matches[0];
    }

    /**
     * @throws ParseException
     */
    private function parseConstant(): mixed {
        foreach (['true', 'false', 'null'] as $const) {
            if (str_starts_with(substr($this->input, $this->pos), $const)) {
                $this->pos += strlen($const);
                return match ($const) {
                    'true' => true,
                    'false' => false,
                    'null' => null,
                };
            }
        }
        throw new ParseException("Неизвестная константа или символ на позиции $this->pos" . PHP_EOL);
    }

    /**
     * @throws ParseException
     */
    private function parseString(): string {
        $this->consume('"');
        $result = '';
        while ($this->peek() !== '"') {
            if ($this->peek() === null) {
                throw new ParseException("Ошибка строки: отсутствует закрывающая кавычка" . PHP_EOL);
            }
            $result .= $this->consume();
        }
        $this->consume('"');
        return $result;
    }

    /**
     * @throws ParseException
     */
    private function parseNumber(): int|float {
        if (!preg_match('/-?\d+(\.\d+)?/A', $this->input, $matches, 0, $this->pos)) {
            throw new ParseException("Ошибка числа на позиции $this->pos" . PHP_EOL);
        }
        $this->pos += strlen($matches[0]);
        return str_contains($matches[0], '.') ? floatval($matches[0]) : intval($matches[0]);
    }

    private function peek(): ?string {
        return $this->pos < strlen($this->input) ? $this->input[$this->pos] : null;
    }

    /**
     * @throws ParseException
     */
    private function consume(?string $char = null): string {
        if ($this->pos >= strlen($this->input)) {
            throw new ParseException("Неожиданный конец ввода" . PHP_EOL);
        }
        $current = $this->input[$this->pos];
        if ($char !== null && $current !== $char) {
            throw new ParseException("Ожидался символ '$char', но получен '$current' (позиция: $this->pos)" . PHP_EOL);
        }
        $this->pos++;
        return $current;
    }

    private function skipWhitespace(): void {
        while ($this->pos < strlen($this->input) && ctype_space($this->input[$this->pos])) {
            $this->pos++;
        }
    }
}