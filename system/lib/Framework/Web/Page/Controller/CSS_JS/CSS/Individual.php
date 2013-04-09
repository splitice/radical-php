<?php

namespace Web\Page\Controller\CSS_JS\CSS;

use Core\Server;
use Web\Page\Controller\CSS_JS\Internal\IndividualBase;

class Individual extends IndividualBase {
	const MIME_TYPE = 'text/css';
	const EXTENSION = 'css';
	static function loadCallback($file, $parser) {
		$paths = array ();
		foreach ( $parser->extensions as $extensionName ) {
			$namespace = ucwords ( preg_replace ( '/[^0-9a-z]+/', '_', strtolower ( $extensionName ) ) );
			$extensionPath = './' . $namespace . '/' . $namespace . '.php';
			if (file_exists ( $extensionPath )) {
				require_once ($extensionPath);
				$hook = $namespace . '::resolveExtensionPath';
				$returnPath = call_user_func ( $hook, $file, $parser );
				if (! empty ( $returnPath )) {
					$paths [] = $returnPath;
				}
			}
		}
		return $paths;
	}
	static function getFunctions($extensions) {
		$output = array ();
		if (! empty ( $extensions )) {
			foreach ( $extensions as $extension ) {
				$name = explode ( '/', $extension, 2 );
				$namespace = ucwords ( preg_replace ( '/[^0-9a-z]+/', '_', strtolower ( array_shift ( $name ) ) ) );
				global $BASEPATH;
				$sass_path = $BASEPATH.'/system/lib/PHPSass/Extensions/';
				$extensionPath = $sass_path.'/' . $namespace . '/' . $namespace . '.php';
				if (file_exists ( $extensionPath )) {
					require_once ($extensionPath);
					$namespace = $namespace . '::';
					$function = 'getFunctions';
					$output = array_merge ( $output, call_user_func ( $namespace . $function, $namespace ) );
				}
			}
		}
		
		return $output;
	}
	static function get_file($file) {
		$ext = pathinfo ( $file, PATHINFO_EXTENSION );
		if ($ext == 'scss' || $ext == 'sass') {
			$bn = basename ( $file );
			if ($bn {0} == '_')
				return ''; // Importable packages
				
			global $BASEPATH;
				$sass_path = $BASEPATH.'/static/img/sprites/';
			$options = array (
					'style' => 'nested',
					'cache' => false,
					'syntax' => $ext,
					'debug' => false,
					//'load_path_functions' => array('loadCallback'),
					'load_paths' => array($sass_path),
					'functions' => self::getFunctions(array('Compass','Own')),
					'extensions' => array('Compass','Own')
			);
			
			if (Server::isProduction ())
				$options ['style'] = 'compressed';

				// Execute the compiler.
			$parser = new \SassParser ( $options );
			return $parser->toCss ( $file );
		}
		
		return file_get_contents ( $file );
	}
}