<?php
namespace Web\Page\Controller\CSS_JS\CSS;
use Core\Server;

use Web\Page\Controller\CSS_JS\Internal\IndividualBase;

class Individual extends IndividualBase {
	const MIME_TYPE = 'text/css';
	const EXTENSION = 'css';
	
	static function get_file($file){
		$ext = pathinfo($file,PATHINFO_EXTENSION);
		if($ext == 'scss' || $ext == 'sass'){
			$options = array(
					'style' => 'nested',
					'cache' => false,
					'syntax' => $ext,
					'debug' => false,
					'callbacks' => array(
							'warn' => 'cb_warn',
							'debug' => 'cb_debug',
					),
			);
			
			if(Server::isProduction())
				$options['style'] = 'compressed';
			
			// Execute the compiler.
			$parser = new \Web\Sass\SassParser($options);
			return $parser->toCss($file);
		}
		
		return file_get_contents($file);
	}
}