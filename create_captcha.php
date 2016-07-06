<?php

include('Captcha/CaptchaBuilderInterface.php');
include('Captcha/PhraseBuilderInterface.php');
include('Captcha/CaptchaBuilder.php');
include('Captcha/PhraseBuilder.php');

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
$phraseBuilder	= new PhraseBuilder();
$captcha = new CaptchaBuilder($phraseBuilder->build(4, '12345677890'));
$captcha->setIgnoreAllEffects(true)
		->setMaxAngle(0)
		->setMaxOffset(0)
		->setInterpolation(false)
		->build(200, 100, 'Captcha/Font/captcha0.ttf');
$filename	= $captcha->getPhrase();
$captcha->save('samples/simple/' . $filename . '.jpg');

