<?php
namespace Utility\Cache\Object\Internal;

abstract class FileCacheBase extends CacheBase {
	protected function cachePath(){
		global $BASEPATH;
		return $BASEPATH.DS.'cache'.DS;
	}
}