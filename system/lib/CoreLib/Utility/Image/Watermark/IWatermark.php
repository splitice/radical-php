<?php
namespace Image\Watermark;

interface IWatermark {
	function applyMark($to_watermark, $to_position);
}