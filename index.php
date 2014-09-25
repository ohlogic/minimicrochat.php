<?php
/*
* minimicrochat.php - the simplest chat server I could write in two hours
*/
$display_size = false;		// display file size for fun, no reason, change to true
$limit_size = 1048576 * 5;	// 5 MB file, prevent file getting too big, preference to size
$file = 'yo_txt.txt';
check_file($file);
if ($_POST['yo']) { sendmessage($file,$_POST['yo']); }
$msgs = getmessages($file, 25);
?><!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>yo</title>
<body onload="document.yo.yo.focus();" style="font-size: 12px; font-family: helvetica, sans-serif;">
<ul>
<?php if (count($msgs)) { foreach ($msgs as $msg) { ?>
<?php $pieces = str_split($msg, $split_length = 11); ?>
<li>
<span style="color: rgb(<?php print($pieces[0]); ?>);">Yo: </span>
<?php print(htmlspecialchars( substr($pieces[1],4)   )) ?>
</li>
<?php }} ?>
</ul>
<form method="post" name="yo">
<input style="width: 80%;" type="text" autocomplete="off" name="yo" />
<button type="submit">yo</button>
</form></body></html>
<?php
// lib
function check_file($f) {
global $display_size;
global $limit_size;
if (file_exists($f) == false){ file_put_contents($f, '');	}
$size = filesize($f);
if ($display_size) {	// optional fun, display file size for no reason
	if (($size) < 1024) $text = $size .'byes'.'<br>';
	if (($size) >=  1024) $text = $size .'byes'.' that is '.number_format($size / 1024, 2).' KB'.'<br>';
	if (($size) >= 1048576) $text = $size .'byes'.' that is '.number_format($bytes / 1048576, 2) . ' MB'.'<br>';
	if (($size) >= 1073741824) $text = $size .'byes'.' that is '.number_format($bytes / 1073741824, 2) . ' GB'.'<br>';
	echo $text; }
// prevent file getting to big...
if ($size >= $limit_size) die('file size exceed, please attend to');	//or just reset file with   file_put_contents($f, '');  //either way, fail-safe
}
function to_file($file, $content){ file_put_contents($file,$content,FILE_APPEND|LOCK_EX); }
function left($str, $length)	 { return substr($str, 0, $length);	}
function right($str, $length)    { return substr($str, -$length);   }
function sendmessage($file, $msg) {
	$text = preg_replace("/[^a-zA-Z0-9]+/", "", $msg);
	$text = str_replace(array("\n", "\r\n"), "", $text);
	$rgb = rgbfromip();
	to_file($file, $rgb . " __ " . left($text, 140) . "\n"); # max length
}
function getmessages($file, $num=25){
	$output = array();
	#exec("tail ".$file." -n ".$num, $output);	//works also, personally, I like the gnu utility tail, more efficient
	$output = without_tail($file, $num);
	return $output;
}
function rgbfromip() {
	$h = array_map('ord', str_split(md5($_SERVER['REMOTE_ADDR'], true)));
	return "$h[0],$h[1],$h[2]";
}
function without_tail($file, $num) {
	return array_slice(file($file), -1 * $num); //PHP's file() function reads the whole file into an array.
}
