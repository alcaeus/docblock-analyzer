<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Documentation;

final class Param
{
    public const REGEX = '#(?P<doc>@param (?P<type>[^\s]+) (?P<name>\$[^\s]+)(?: (?P<description>.*))?)#';

    private $doc;

    private $types;

    private $name;

    private $description;

    public function __construct($doc)
    {
        $matches = [];
        if (!preg_match(self::REGEX, $doc, $matches)) {
            throw new \Exception('Invalid doc given');
        }

        $this->doc = $matches['doc'];
        $this->types = $matches['type'];
        $this->name = $matches['name'];
        $this->description = $matches['description'] ?? null;
    }

    public function hasDescription(): bool
    {
        return !empty($this->description);
    }
}
