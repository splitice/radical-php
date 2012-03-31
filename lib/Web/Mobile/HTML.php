<?php
namespace Web\Mobile;

class HTML extends Config {
	static function Build(){
		$ret = array();
		
		//Viewport
		if(static::$VIEWPORT){
			if(!is_array(static::$VIEWPORT)){
				static::$VIEWPORT = array(static::$VIEWPORT);
			}
			foreach(static::$VIEWPORT as $v){
				$ret[] = new \HTML\Tag\Meta('viewport', $v);
			}
		}
		
		//Hide Chrome
		if(static::$HIDE_CHROME){
			$ret[] = new \HTML\Tag\Meta('apple-mobile-web-app-capable', 'yes');
		}else{
			$ret[] = new \HTML\Tag\Meta('apple-mobile-web-app-capable', 'no');
		}
		
		//Web App Icon
		if(static::$ICON){
			if(is_string(static::$ICON)){
				$ret[] = new \HTML\Tag\Link('apple-touch-icon', static::$ICON);
			}elseif(is_array(static::$ICON)){
				foreach(static::$ICON as $size=>$icon){
					$name = 'apple-touch-icon';
					if(is_array($icon)){
						if(isset($icon['precomposed']) && $icon['precomposed']){
							$name = 'apple-touch-icon-precomposed';
						}
						if(isset($icon['src'])){
							$icon = $icon['src'];
						}else{
							continue;
						}
					}
					$link = new \HTML\Tag\Link($name, $icon);
					if($size != '57x57'){
						$link->attributes['sizes'] = $size;
					}
					$ret[] = $link;
				}
			}
		}
		
		//Loading Pages
		if(static::$LOADSCREEN){
			foreach(static::$MODELS as $model=>$media){
				$loadScreen = $rotate = false;
				if(is_string(static::$LOADSCREEN)){
					$loadScreen = static::$LOADSCREEN;
				}elseif(isset(static::$LOADSCREEN[$model])){
					$loadScreen = static::$LOADSCREEN[$model];
				}elseif(substr($model,0,-10)=='_landscape' && isset(static::$LOADSCREEN[$m = substr($model,0,10)])){
					$loadScreen = static::$LOADSCREEN[$m];
					$rotate = true;
				}elseif(isset(static::$LOADSCREEN[$model.'_landscape'])){
					$loadScreen = static::$LOADSCREEN[$model.'_landscape'];
					$rotate = true;
				}
				
				if($loadScreen){
					//TODO: Rotate and Resize
					$ret[] = new \HTML\Tag\Link('apple-touch-startup-image', $loadScreen, $media);
				}
			}
		}
		
		//Build String
		$string = '';
		foreach($ret as $v){
			$string .= (string)$v;
		}
		
		return $string;
	}
	static function Output(){
		echo static::Build();
	}
}