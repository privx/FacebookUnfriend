<?php
error_reporting(0);
set_time_limit(0);
require_once('color.php');

function friendlist($token){
	$a = file_get_contents('https://graph.facebook.com/me/friends?access_token='.$token);
	return $a;
}
function last_active($id, $tok){
	$a = json_decode(file_get_contents('https://graph.facebook.com/'.$id.'/feed?access_token='.$tok.'&limit=1'), true);
	$date = $a['data'][0]['created_time'];
	$aa = strtotime($date);
	return date('Y', $aa);
}

function unfriend($id, $token){
	$url = 'https://graph.facebook.com/me/friends?uid='.$id.'&access_token='.$token;
	$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);
	if($result == true){
		$unf = Console::green('[UNFRIENDED]');
	} else {
		$unf = Console::red('[FAILED TO UNFRIEND]');
	}
	return $unf;
}

echo Console::blue("     Facebook Auto Unfriend\n");
echo Console::blue("        Inactive Users\n\n");

//INPUT
echo "Facebook token : ";
$fbtoken = trim(fgets(STDIN));
echo "Year : ";
$year = trim(fgets(STDIN));
echo "\n";

$FL = json_decode(friendlist($fbtoken),true)['data'];
$jumlah = count($FL);
$sukses=0;
$hitung=0;
foreach($FL as $list){
	$hitung = $hitung+1;
	$name = $list['name'];
	$id = $list['id'];
	$date = last_active($id, $fbtoken);
	if($date < $year){
		$sukses++;
		$result = Console::red('[INACTIVE]').' '.$name.' ~ '.$date.' '.unfriend($id, $fbtoken)."\r\n";
	}else{
		$result = Console::green('[ACTIVE]').' '.$name.' ~ '.$date."\r\n";
	}
	echo "[$hitung/$jumlah] $result";
}

echo PHP_EOL."Successfully removed: ".$sukses;
