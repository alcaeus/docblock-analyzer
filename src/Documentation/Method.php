<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Documentation;

use Alcaeus\DocblockAnalyzer\Parser\DocParser;

final class Method
{
    private $method;

    private $description;

    private $params;

    private $return;

    private $throws;

    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;
        $this->description = DocParser::getDescription($method->getDocComment());
        $this->params = DocParser::getParams($method->getDocComment());
        $this->return = DocParser::getReturn($method->getDocComment());
        $this->throws = DocParser::getThrows($method->getDocComment());
    }

    public function hasDescription(): bool
    {
        return !empty($this->description);
    }

    public function getParams(?callable $p = null): array
    {
        return is_callable($p) ? array_filter($this->params, $p) : $this->params;
    }

    public function getReturnStatement(?callable $p = null): ?Statement
    {
        return is_callable($p) && $this->return ? (call_user_func($p, $this->return) ? $this->return : null) : $this->return;
    }

    public function getThrows(?callable $p = null): array
    {
        return is_callable($p) ? array_filter($this->throws, $p) : $this->throws;
    }
}
