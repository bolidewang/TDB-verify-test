<?php
$ch	= curl_init("https://kyfw.12306.cn/otn/passcodeNew/getPassCodeNew?module=login&rand=sjrand");
for ($i = 0; $i < 10000; $i++) {
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$content	= curl_exec($ch);
	file_put_contents('tmp/' . $i . '.png', $content); 
}
