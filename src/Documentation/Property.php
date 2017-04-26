<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Documentation;

use Alcaeus\DocblockAnalyzer\Parser\DocParser;

final class Property
{
    private $property;

    private $description;

    private $var;

    public function __construct(\ReflectionProperty $property)
    {
        $this->property = $property;
        $this->description = DocParser::getDescription((string) $property->getDocComment());
        $this->var = DocParser::getVar((string) $property->getDocComment());
    }

    public function hasDescription(): bool
    {
        return !empty($this->description);
    }
}
