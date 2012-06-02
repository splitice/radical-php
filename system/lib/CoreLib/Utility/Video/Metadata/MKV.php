<?php
namespace Utility\Video\Metadata;

class MKV extends Internal\MetadataBase implements IMetaData {
	protected $supports = array('title','description','language');
}