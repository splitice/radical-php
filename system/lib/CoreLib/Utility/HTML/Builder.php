<?php
namespace HTML;

class Builder {
	static function Script($src,$ext=true){
		if($ext){
			return '<script src="'.$src.'"></script>';
		}else{
			return '<script>'.$src.'</script>';
		}
	}
}