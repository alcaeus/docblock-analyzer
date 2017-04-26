<?php declare(strict_types = 1);

namespace Test;

abstract class Test3Parent
{
    /**
     * @return void Nothing
     */
    public function something()
    {
    }

    abstract public function bleh();
}

final class Test3Child extends Test3Parent
{
    /**
     * {@inheritdoc}
     */
    public function bleh()
    {
    }
}
