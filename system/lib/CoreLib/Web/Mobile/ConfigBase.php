<?php
namespace Web\Mobile;

abstract class ConfigBase extends \Core\Object {
	static $IS_WEBAPP = false;
	static $HIDE_CHROME = false;
	static $LOADSCREEN = null;//string or array of MODEL=>screen
	static $ICON = null;//string or array of SIZE=>icon or SIZE=>aray('src'=>icon,'precomposed'=>true)
	static $VIEWPORT = array(
	//						'width=device-width, initial-scale=1, maximum-scale=1',
							'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
						);
	
	static $MODELS = array(
			//Older iPad
			'ipad'=>'screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) and (-webkit-max-device-pixel-ratio: 1)', 
			'ipad_landscape'=>'screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) and (-webkit-max-device-pixel-ratio: 1)',
			
			//Retina iPad
			'ipad_retina'=>'screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio: 2)',
			'ipad_landscape_retina'=>'screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio: 2)',
			
			//iPhone
			'iphone'=>'screen and (min-device-width: 200px) and (max-device-width: 320px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio: 1)',
			'iphone_landscape'=>'screen and (min-device-width: 200px) and (max-device-width: 320) and (orientation:portrait) and (-webkit-min-device-pixel-ratio: 1)',
	
			//Retina iPhone
			'iphone_retina'=>'screen and (min-device-width: 200px) and (max-device-width: 320px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio: 2)',
			'iphone_landscape_retina'=>'screen and (min-device-width: 200px) and (max-device-width: 320px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio: 2)',
		);
}