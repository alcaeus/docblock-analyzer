<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Documentation;

class Statement
{
    private $doc;

    private $statement;

    private $type;

    private $name;

    private $description;

    public function __construct(string $doc, string $statement, string $type, ?string $name, ?string $description)
    {
        $this->doc = $doc;
        $this->statement = $statement;
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
    }

    public static function createForResult(array $parseResult): self
    {
        if (!isset($parseResult['doc'], $parseResult['statement'], $parseResult['type'])) {
            throw new \InvalidArgumentException('Invalid parse result given; needs doc, statement and type at the least');
        }

        return new self($parseResult['doc'], $parseResult['statement'], $parseResult['type'], $parseResult['name'] ?? null, $parseResult['description'] ?? null);
    }

    public function hasDescription(): bool
    {
        return !empty($this->description);
    }

    public function hasMultipleTypes(): bool
    {
        return strpos($this->type, '|') !== false;
    }

    public function isWeakType(): bool
    {
        return (bool) preg_match('#(^|\|)array(\||$)#i', $this->type);
    }

    public function isVoid(): bool
    {
        return (bool) preg_match('#(^|\|)void(\||$)#i', $this->type);
    }
}
