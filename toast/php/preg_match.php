<?php
//$p = 'CourseTitle';
//echo preg_match('/(^[A-Z]{1}[a-z]+)([A-Z]{1}[a-z]+[a-z]$)?/', $p, $r);
//var_dump($r);
//$p = 'getByLessons';
//echo preg_match('/^getBy(\w+)?$/', $p, $r);
//var_dump($r);
//echo preg_match('/^get(\w+?)(?:By(\w+))?$/', $p, $r);
//var_dump($r);
//$p = 'ClassOfMember';
//echo preg_match('/^(\w+)(?:Of)(\w+)/', $p, $r);
//var_dump($r);
//$p = 'ClassasfafNNN';
//echo preg_match('/^[A-Z]+\w+/', $p, $r);
//var_dump($r);
//$p = 'getCoursesByLessons';
//echo preg_match('/^getBy(\w+)?$/', $p, $r);
//var_dump($r);
//
//$p = 'getCourseByLesson';
//echo preg_match('/^get(\w+?)?(?:By(\w+))+$/', $p, $r);
//var_dump($r);
//
//$p = 'getViaLesson';
//echo preg_match('/^get(\w+?)?(?:Via(\w+))+$/', $p, $r);
//var_dump($r);
//
$p = 'this, "somecmpany, llc", "and ""this"" w,o.rks", foo bar'; 
$regexp = '%(?:^|,\ *)("(?>[^"]*)(?>""[^"]* )*"|(?: [^",]*))%x'; 
preg_match_all($regexp, $p, $matches);

$p = 'getByLessonAndCourse'; 
//'/^find(\w+?)Via(\w+?)(?:By(\w+?)(?:And(\w+))?)?$/'
//$p .= 'And';
preg_match('/^(get|update|delete)(?:By(\w+?)(?:And(\w+))?)?$/', $p, $matches);
preg_match('/^get(\w+?)Via(\w+?)(?:By(\w+?)(?:And(\w+))?)?$/', $p, $matches);

//$matches[3] = str_replace('And', '', $matches[3]);
//if (empty($matches[2])) unset($matches[2]); 
preg_match('/^get(?:By(\w+?)(?:And(\w+))?)+$/', $p, $matches);

echo 'haha';

$p = 'This_is_9_test';
preg_match('/[a-zA-Z0-9]+(:?_)/', $p, $matches);
var_dump($matches);


?>