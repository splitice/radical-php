<?php
namespace Web\Mobile;

abstract class ConfigBase extends \Core\Object {
	static $HIDE_CHROME = false;
	static $LOADSCREEN = null;//string or array of MODEL=>screen
	static $ICON = null;//string or array of SIZE=>icon or SIZE=>aray('src'=>icon,'precomposed'=>true)
	static $VIEWPORT = array(
							'width=device-width, initial-scale=1, maximum-scale=1',
							'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
						);
	
	static $MODELS = array(
			'ipad'=>'screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)', 
			'ipad_landscape'=>'screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)', 
			'iphone'=>'screen and (min-device-width: 200px) and (max-device-width: 320px)',
			'iphone_landscape'=>'screen and (min-device-width: 200px) and (max-device-width: 320) and (orientation:portrait)');
}