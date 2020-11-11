<?php

session_start();

define('GOOGLE_ID','xxxxxxxxxx');

define('GOOGLE_SECRET','xxxxxxxxxx');

define('GOOGLE_REDIRECT_URL','xxxxxxxxxx');

if(!empty($_REQUEST['code'])){
	$url = 'https://accounts.google.com/o/oauth2/token';			
	$curlPost = 'client_id='.GOOGLE_ID.'&redirect_uri='.urlencode(GOOGLE_REDIRECT_URL).'&client_secret='.GOOGLE_SECRET.'&code='.$_REQUEST['code'].'&grant_type=authorization_code';
	$ch = curl_init();		
	curl_setopt($ch, CURLOPT_URL, $url);		
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
	curl_setopt($ch, CURLOPT_POST, 1);		
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
	$data = json_decode(curl_exec($ch), true);
	if(!empty($data['access_token'])){
		$url = 'https://www.googleapis.com/oauth2/v2/userinfo?fields=email,verified_email';	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$data['access_token']]);
		$data2 = json_decode(curl_exec($ch), true);
		if(!empty($data2['email']) and !empty($data2['verified_email'])){
			$_SESSION['logged_in'] = true;
			$_SESSION['email'] = $data2['email'];
		}
	}
}

if(empty($_SESSION['logged_in'])){
	$google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me') . '&redirect_uri=' . urlencode(GOOGLE_REDIRECT_URL) . '&response_type=code&client_id=' . GOOGLE_ID . '&access_type=online';
}

?>
<html>
<head>
	<title>Logowanie przez Google</title>
</head>

<body>
	
	<?php
		if(empty($_SESSION['logged_in'])){
	?>
			<a href="<?= $google_login_url ?>">Zaloguj przez konto Google</a>
	<?php
		}elseif(!empty($_SESSION['email'])){
			echo('Witaj '.$_SESSION['email']);
		}
	?>

</body>
</html>
