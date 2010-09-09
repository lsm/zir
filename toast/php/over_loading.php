<?php
class Foo
{
    public function __call($method, array $args)
    {
         var_dump($method);
         //var_dump($args);
         return $this;
    }
    
    public function __get($name)
    {
        var_dump($name);
        return $this;
    }
}


$foo = new Foo();

$foo->Course->update();

$foo->Course()->update();
?>