<!doctype html>
<meta charset=utf-8>
<title>大五碼 碼表產生器 (試玩)</title>
<style>
html{background:#eee}
body{font:16px/1.2 Roboto,Arial,DFKai-SB,Kaiti,sans-serif;max-width:640px;margin:0 auto;background:#fff;padding:20px}
h2{position:sticky;top:0;padding:10px 20px;margin:20px -20px 10px;border-bottom:1px solid #ccc;background:#f3f3f3}
table{width:100%}
table,td{border:1px solid;border-collapse:collapse}
td{padding:10px 0;width:72px;text-align:center}
.he{background:#eee}
</style>
<div><a href="#b51">Big-5 Level 1</a> | <a href="#b52">Big-5 Level 2</a> | <a href="#scs">HKSCS</a></div>
<div>HKSCS碼表來自: https://data.gov.hk/en-data/dataset/hk-ogcio-ogcio_hp-hong-kong-supplementary-character-set-related-information/resource/009a5bf1-7f40-4bfb-acbb-8851bf832708</div>
<?php
$big5 = file_get_contents('big5-table.txt');

$charList = [];
$lines = explode("\n", $big5);
foreach ($lines as $line) {
	if ($line === '') {
		continue;
	}
	$chars = explode(' ', substr(trim($line), 6));
	$prefix = hexdec(substr($line, 0, 4));
	if ($chars[0] == '' && $chars[1] == '') {
		array_shift($chars);
		array_shift($chars);
	}
	for ($i = 0; $i < 16; $i++) {
		if (isset($chars[$i]) && $chars[$i] !== '') {
			$charList[dechex($prefix + $i)] = [dechex($prefix + $i), $chars[$i]];
		}
	}
}


$big52 = file_get_contents('big5-table2.txt');

$charList2 = [];
$lines = explode("\n", $big52);
foreach ($lines as $line) {
	if ($line === '') {
		continue;
	}
	$chars = explode(' ', substr(trim($line), 6));
	$prefix = hexdec(substr($line, 0, 4));
	if ($chars[0] == '' && $chars[1] == '') {
		array_shift($chars);
		array_shift($chars);
	}
	for ($i = 0; $i < 16; $i++) {
		if (isset($chars[$i]) && $chars[$i] !== '') {
			$charList2[dechex($prefix + $i)] = [dechex($prefix + $i), $chars[$i]];
		}
	}
}

usort($charList, function($a, $b) {
	return strcmp($a[1], $b[1]);
});
usort($charList2, function($a, $b) {
	return strcmp($a[1], $b[1]);
});

?>
<h2 id=b51>Big 5 Level 1</h2>
<table border=1 style="border-collapse:collapse" cellpadding=10>
<?
$chunk = array_chunk($charList, 8);
foreach ($chunk as $row) {
	echo '<tr>';
	foreach ($row as $col) {
		$utf32 = iconv('UTF-8', 'UTF-32BE', trim($col[1]));
		echo '<td align=center>' . strtoupper($col[0]) . '<div style="font-size:32px">' . $col[1] . '</div>' . strtoupper(ltrim(bin2hex($utf32), "0")) . '</td>';
	}
	echo '</tr>' . "\r\n";
}
?>
</table>
<h2 id=b52>Big 5 Level 2</h2>
<table border=1 style="border-collapse:collapse" cellpadding=10>
<?
$chunk = array_chunk($charList2, 8);
foreach ($chunk as $row) {
	echo '<tr>';
	foreach ($row as $col) {
		$utf32 = iconv('UTF-8', 'UTF-32BE', trim($col[1]));
		echo '<td align=center>' . strtoupper($col[0]) . '<div style="font-size:32px">' . $col[1] . '</div>' . strtoupper(ltrim(bin2hex($utf32), "0")) . '</td>';
	}
	echo '</tr>' . "\r\n";
}
?>
</table>

<?

function get_content($URL){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $URL);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function removeBOM($data) {
    if (0 === strpos(bin2hex($data), 'efbbbf')) {
       return substr($data, 3);
    }
	return $data;
}

$hkscs = get_content('https://www.ogcio.gov.hk/tc/our_work/business/tech_promotion/ccli/hkscs/doc/HKSCS2016.json');
$json = json_decode(trim(removeBOM($hkscs)));
$charListHKSCS = [];
foreach($json as $item) {
	if ($item->cangjie !== "") {
		if (substr($item->{"H-Source"}, 0, 2) === 'H-') {
			$code = substr($item->{"H-Source"}, 2);
		} else {
			$code = $item->{"H-Source"};
		}
		if (!isset($charList[$code]) && !isset($charList2[$code])) {
			$charListHKSCS[$code] = [$code, $item->char];
		}
	}
}
?>
<h2 id=scs>HKSCS</h2>
<table border=1 style="border-collapse:collapse" cellpadding=10>
<?
$chunk = array_chunk($charListHKSCS, 8);
foreach ($chunk as $row) {
	echo '<tr>';
	foreach ($row as $col) {
		$utf32 = iconv('UTF-8', 'UTF-32BE', trim($col[1]));
		if (strlen($col[0]) > 4) {
			$class = ' class="he';
			$row1 = '<span style="font-family:Arial Narrow">' . strtoupper($col[0]) . '</span>';
		} else {
			$class = '';
			$row1 = strtoupper($col[0]);
		}
		echo '<td align=center' . $class . '>' . $row1 . '<div style="font-size:32px">' . $col[1] . '</div>' . strtoupper(ltrim(bin2hex($utf32), "0")) . '</td>';
	}
	echo '</tr>' . "\r\n";
}
?>
</table>
