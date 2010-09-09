<?php


$name = 'testTemplate.php';
$file = & file_get_contents($name, true);

preg_match_all('/{[$_\[\]a-zA-Z0-9]+}/', $file, $j);

		$open = strpos($file, '{[_users]}');
		$close = strpos($file, '{[users_]}');
		$tagLen = strlen('{[_users]}');

		$blockPattern = substr($file, $open, $close - $open + $tagLen);
		//$blockPattern = '{[_users]}{[_users]}{[_users]}';
		$blockPattern = str_replace("\r", '', $blockPattern);
var_dump($blockPattern);
var_dump(preg_replace("/^({\[_)([.\n]*)(\]})$/", 'HHHHHHHHHH',$blockPattern));

for ($i = 0 ; $i < count($j[0]) ; $i++) {

			if(preg_match('/^{[a-z]+}$/', $j[0][$i])) {
				$segments[$i]['name'] = preg_replace('/(^{)|(}$)/','',$j[0][$i]);
				$segments[$i]['type'] = 'FILE';
				$segments[$i]['path'] = 'BASE';
				$segments[$i]['real_name'] = $j[0][$i];
			}

			if(preg_match('/^{_[a-z]+}$/', $j[0][$i])) {
				$segments[$i]['name'] = preg_replace('/(^{_)|(}$)/','',$j[0][$i]);
				$segments[$i]['type'] = 'FILE';
				$segments[$i]['path'] = 'THIS';
				$segments[$i]['real_name'] = $j[0][$i];
			}

			if(preg_match('/{_SELF}/', $j[0][$i])) {
				$segments[$i]['name'] = $name;
				$segments[$i]['type'] = 'FILE';
				$segments[$i]['path'] = 'THIS';
				$segments[$i]['real_name'] = $j[0][$i];
			}

			if(preg_match('/^{\[_[a-z]+\]}$/', $j[0][$i], $type)) {
				$segments[$i]['type'] = 'BLOCK';
				$segments[$i]['open'] = $j[0][$i];
				$segments[$i]['name'] = preg_replace('/(^{\[_)|(\]}$)/','',$j[0][$i]);

				$cur = $i;
				$name = $segments[$i]['name'];

				for($n = ++$i; preg_match('/^{\['.$name.'_\]}$/', $j[0][$n]) == false; $n++, $i++) {
					if(preg_match('/^{\$[a-zA-Z0-9]+}$/', $j[0][$n])) {
						$keys[] = preg_replace('/(^{\$)|(}$)/','',$j[0][$i]);
					}
				}
				$segments[$cur]['close'] = $j[0][$n];
				$segments[$cur]['keys'] = $keys;
				$keys = null;

			} /*elseif(preg_match('/^{\[[a-z]+_\]}$/', $j[0][$i], $type)) {
			$segments[$i]['name'] = preg_replace('/(^{\[)|(_\]}$)/','',$j[0][$i]);
			$segments[$i]['type'] = 'BLOCK_END';
			$segments[$i]['real_name'] = $j[0][$i];
			}*/

			if(preg_match('/^{\$[a-zA-Z0-9]+}$/', $j[0][$i])) {
				$segments[$i]['name'] = preg_replace('/(^{\$)|(}$)/','',$j[0][$i]);
				$segments[$i]['type'] = 'VAR';
				$segments[$i]['real_name'] = $j[0][$i];
			}

		}
		
		
		var_dump($j[0]);

var_dump($segments);


?>