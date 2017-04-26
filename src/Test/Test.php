<?php declare(strict_types = 1);

final class Test
{
    /**
     * @var array
     */
    private $foo;

    /**
     * Just a sample var
     * @var string
     */
    private $bar;

    /**
     * Yay, it's a description!
     * @param int $foo Description bla bla bla
     * @param int $bar Description bleh bleh bleh bleh bleh bleh bleh bleh bleh
     * Newline bleh bleh bleh bleh bleh
     * @param int $bar
     * @return void Useless description
     */
    public function methodWithDocblocks()
    {

    }

    /**
     * @param int $foo Description bla bla bla
     * @param int $bar Description bleh bleh bleh
     * @return Foo|ReflectionClass
     * @throws \Exception Bleh
     * @throws \RuntimeException
     */
    public function methodOtherWithDocblocks()
    {

    }
}
