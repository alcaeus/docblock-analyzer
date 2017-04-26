<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Documentation;

use Alcaeus\DocblockAnalyzer\Functions;

final class ClassDocumentation
{
    private $class;

    private $properties;

    private $methods;

    public function __construct(\ReflectionClass $class)
    {
        $this->class = $class;
        $this->properties = array_map(function (\ReflectionProperty $property) { return new Property($property); }, $class->getProperties());
        $this->methods = array_map(function (\ReflectionMethod $method) { return new Method($method); }, $class->getMethods());
    }

    public static function createForClass(string $className): self
    {
        return new self(new \ReflectionClass($className));
    }

    public function getProperties(?callable $p = null): array
    {
        return is_callable($p) ? array_filter($this->properties, $p) : $this->properties;
    }

    public function getMethods(?callable $p = null): array
    {
        return is_callable($p) ? array_filter($this->methods, $p) : $this->methods;
    }

    public function getParams(?callable $methodClosure = null, ?callable $paramClosure = null): array
    {
        $params = array_map(function (Method $method) use ($paramClosure) {
            return $method->getParams($paramClosure);
        }, $this->getMethods($methodClosure));
        return Functions\array_flatten($params);
    }

    public function getReturnStatements(?callable $methodClosure = null, ?callable $paramClosure = null): array
    {
        $methods = array_map(function (Method $method) use ($paramClosure) {
            return $method->getReturnStatement($paramClosure);
        }, $this->getMethods($methodClosure));
        return array_filter($methods, function ($method) { return $method !== null; });
    }

    public function getThrows(?callable $methodClosure = null, ?callable $throwsClosure = null): array
    {
        $throws = array_map(function (Method $method) use ($throwsClosure) {
            return $method->getThrows($throwsClosure);
        }, $this->getMethods($methodClosure));
        return Functions\array_flatten($throws);
    }
}
