<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Documentation;

use Alcaeus\DocblockAnalyzer\Functions;

final class ClassCollection implements \Countable
{
    private $classes = [];

    public static function create(array $classes): self
    {
        $instance = new self();
        $instance->classes = array_map([ClassDocumentation::class, 'createForClass'], $classes);

        return $instance;
    }

    public function getProperties(?callable $p = null): array
    {
        $properties = array_map(function (ClassDocumentation $class) use ($p) {
            return $class->getProperties($p);
            }, $this->classes);
        return Functions\array_flatten($properties);
    }

    public function getMethods(?callable $p = null): array
    {
        $methods = array_map(function (ClassDocumentation $class) use ($p) {
            return $class->getMethods($p);
        }, $this->classes);
        return Functions\array_flatten($methods);
    }

    public function getParams(?callable $methodClosure = null, ?callable $paramClosure = null): array
    {
        $params = array_map(function (ClassDocumentation $class) use ($methodClosure, $paramClosure) {
            return $class->getParams($methodClosure, $paramClosure);
        }, $this->classes);
        return Functions\array_flatten($params);
    }

    public function getReturnStatements(?callable $methodClosure = null, ?callable $returnStatementClosure = null): array
    {
        $returnStatements = array_map(function (ClassDocumentation $class) use ($methodClosure, $returnStatementClosure) {
            return $class->getReturnStatements($methodClosure, $returnStatementClosure);
        }, $this->classes);
        return Functions\array_flatten($returnStatements);
    }

    public function getThrows(?callable $methodClosure = null, ?callable $throwsClosure = null): array
    {
        $throws = array_map(function (ClassDocumentation $class) use ($methodClosure, $throwsClosure) {
            return $class->getThrows($methodClosure, $throwsClosure);
        }, $this->classes);
        return Functions\array_flatten($throws);
    }

    public function analyzeMethods(): array
    {
        return [
            'number' => count($this->getMethods()),
            'withDescription' => count($this->getMethods(function (Method $method) { return $method->hasDescription(); })),
            'withInheritDoc' => count($this->getMethods(function (Method $method) { return $method->isInheritDoc(); })),
        ];
    }

    public function analyzeProperties(): array
    {
        return [
            'number' => count($this->getProperties()),
            'withDescription' => count($this->getProperties(function (Property $property) { return $property->hasDescription(); })),
        ];
    }

    public function analyzeMethodParams(): array
    {
        $params = count($this->getParams());
        $paramsWithDescription = count($this->getParams(null, function (Statement $param) { return $param->hasDescription(); }));
        $paramsWithMultipleTypes = count($this->getParams(null, function (Statement $param) { return $param->hasMultipleTypes(); }));
        $paramsWithWeakType = count($this->getParams(null, function (Statement $param) { return $param->isWeakType(); }));

        return [
            'number' => $params,
            'withDescription' => $paramsWithDescription,
            'withMultipleTypes' => $paramsWithMultipleTypes,
            'withWeakType' => $paramsWithWeakType,
        ];
    }

    public function analyzeMethodReturnStatements(): array
    {
        $returnStatements = count($this->getReturnStatements());
        $returnStatementsWithDescription = count($this->getReturnStatements(null, function (Statement $return) { return $return->hasDescription(); }));
        $returnStatementsWithMultipleTypes = count($this->getReturnStatements(null, function (Statement $return) { return $return->hasMultipleTypes(); }));
        $returnStatementsWithWeakType = count($this->getReturnStatements(null, function (Statement $return) { return $return->isWeakType(); }));
        $returnStatementsWithVoid = count($this->getReturnStatements(null, function (Statement $return) { return $return->isVoid(); }));

        return [
            'number' => $returnStatements,
            'withDescription' => $returnStatementsWithDescription,
            'withMultipleTypes' => $returnStatementsWithMultipleTypes,
            'withWeakType' => $returnStatementsWithWeakType,
            'withVoid' => $returnStatementsWithVoid,
        ];
    }

    public function analyzeMethodThrows(): array
    {
        $throws = count($this->getThrows());
        $throwsWithDescription = count($this->getThrows(null, function (Statement $throws) { return $throws->hasDescription(); }));
        $throwsWithMultipleTypes = count($this->getThrows(null, function (Statement $throws) { return $throws->hasMultipleTypes(); }));

        return [
            'number' => $throws,
            'withDescription' => $throwsWithDescription,
            'withMultipleTypes' => $throwsWithMultipleTypes,
        ];
    }

    public function count()
    {
        return count($this->classes);
    }
}
