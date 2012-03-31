<?php
namespace Video\Metadata;

class MOV extends Internal\MetadataBase implements IMetaData {
	protected $supports = array('title','author','composer','album','year','track','comment','genre','copyright','description','synopsis','show','episode_id','network');
}