<?php
namespace Video\Metadata;

class WMV extends Internal\MetadataBase implements IMetaData {
	protected $supports = array('title','author','copyright','comment');
}