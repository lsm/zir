<?php
$testArray = array(
                              '/js/global_js/head2.js' => '<script src="http://image.360quan.com/js/global_js/head2.js" type="text/javascript"></script>',
                              '/js/nav.js' => '<script type="text/javascript" src="http://image.360quan.com/js/nav.js"></script>',
                              '/js/space/show_flash.js' => '<script src="http://image.360quan.com/js/space/show_flash.js" type="text/javascript" ></script>',
                             );
$s = microtime(true);
for ($i = 0; $i < 100000; $i++) {
serialize($testArray);    
}
echo microtime(true) - $s . "\n";


$s = microtime(true);
for ($i = 0; $i < 100000; $i++) {
implode('_', $testArray);    
}
echo microtime(1) - $s . "\n"; 


?>