<?php
//$binarydata = pack("nvc*", 0x1234, 0x5678, 65, 66);
//echo pack("C3",80,72,80);

//echo pack("C*",80,72,80);
//var_dump(unpack("C*","刘"));
$bin = pack("c*", 'a', 'b', 'c', 'd', 'e');
//var_dump(pack("C", 'Hello php!'));
//var_dump(unpack("C*", 'PHP'));

//$d = 'haaha1';
//sprintf("%'\0-4s", pack("L*", $d));
echo $bin;
?>
