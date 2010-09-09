<?php

$c = file_get_contents('http://www.google.com/search?q=%E9%AD%94%E5%85%BD%E4%B8%96%E7%95%8C&ie=utf-8&oe=utf-8&aq=t&rls=org.mozilla:en-US:official&client=firefox-a');

//$c = 'Results <b>1</b> - <b>10</b> of about <b>11,800,000</b> for <b>python </b>';

preg_match('/about <b>([0-9,]+)/', $c, $m);
var_dump($m);
echo str_replace(',', '',$m[1]);

//echo (file_get_contents('http://news.google.com/news?ned=cn&hl=zh-CN&ned=cn&q=%E4%B9%90%E6%88%90&btnG=%E6%90%9C%E7%B4%A2'));
