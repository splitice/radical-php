<?php
namespace Video\Metadata;

class AVI extends Internal\MetadataBase implements IMetaData {
	protected $supports = array('Title','Artist','Copyright','Album','Genre','Track');
}