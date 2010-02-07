<?php
class Test_BeforeFoo extends Gene_Service
{
    public function init()
    {
    }

    public function beforeFoo()
    {
        echo __FUNCTION__;
    }

    public function foo()
    {
        echo __FUNCTION__;
    }

    public function beforeBar()
    {
        echo __FUNCTION__;
    }

    public function bar($args)
    {
        echo $args . __FUNCTION__;
    }

}
class Test_AfterFoo extends Gene_Service
{
    public function init()
    {
    }

    public function foo()
    {
        echo __FUNCTION__;
    }

    public function afterFoo()
    {
        echo __FUNCTION__;
    }

    public function afterBar()
    {
        echo __FUNCTION__;
    }

    public function bar($args)
    {
        echo $args . __FUNCTION__;
    }


}

class Test_AroundFoo extends Gene_Service
{
    public function init()
    {
    }

    public function foo()
    {
        echo __FUNCTION__;
    }

    public function aroundFoo()
    {
        echo __FUNCTION__;
    }

    public function aroundBar($args)
    {
        echo $args . __FUNCTION__;
    }
}

