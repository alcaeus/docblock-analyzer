<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Parser;

use Alcaeus\DocblockAnalyzer\Documentation\Statement;

final class DocParser
{
    private const REGEX_TYPE = '(?P<type>[^\s]+)';
    private const REGEX_DESCRIPTION = '(?: (?P<description>.*))?';
    private const REGEX_NAME = '(?P<name>\$[^\s]+)';

    private const VAR_REGEX = '#\s*\* (?P<doc>@(?<statement>var) ' . self::REGEX_TYPE . self::REGEX_DESCRIPTION . ')#';
    private const RETURN_REGEX = '#\s*\* (?P<doc>@(?<statement>return) ' . self::REGEX_TYPE . self::REGEX_DESCRIPTION . ')#';
    private const PARAM_REGEX = '#\s*\* (?P<doc>@(?<statement>param) ' . self::REGEX_TYPE . ' ' . self::REGEX_NAME . self::REGEX_DESCRIPTION . ')#';
    private const THROWS_REGEX = '#\s*\* (?P<doc>@(?<statement>throws) ' . self::REGEX_TYPE . self::REGEX_DESCRIPTION . ')#';
    private const DESCRIPTION_REGEX = '#/\*\*\\n.*?\* (?P<description>[^@].*)#m';

    public static function getDescription(string $comment): ?string
    {
        if (!preg_match(self::DESCRIPTION_REGEX, $comment, $matches)) {
            return null;
        }

        return $matches['description'];
    }

    public static function getVar(string $comment): ?Statement
    {
        return self::getSingleDocComment($comment, self::VAR_REGEX);
    }

    public static function getReturn(string $comment): ?Statement
    {
        return self::getSingleDocComment($comment, self::RETURN_REGEX);
    }

    public static function getParams(string $comment): array
    {
        return self::getDocComments($comment, self::PARAM_REGEX);
    }

    public static function getThrows(string $comment): array
    {
        return self::getDocComments($comment, self::THROWS_REGEX);
    }

    private static function getSingleDocComment(string $comment, string $regex): ?Statement
    {
        return self::getDocComments($comment, $regex)[0] ?? null;
    }

    private static function getDocComments(string $comment, string $regex): array
    {
        $matches = [];
        preg_match_all($regex, $comment, $matches, PREG_SET_ORDER);

        return array_map(function ($match) {
            return Statement::createForResult($match);
        }, $matches);
    }
}
