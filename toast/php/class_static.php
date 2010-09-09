<?php
abstract class A 
{
    public static $_p = 0;
    public function getP(){
        return self::$_p;
    }
}

class B extends A
{
    public static $_p = 1;
    
    function __construct()
    {
        self::$_p = 1;
    }
    
//    public function getP() 
//    {
//        return self::$_p;
//    }
}
$b = new B();
var_dump($b->getP());
?>