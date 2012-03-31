<?php
namespace Image\Filters;

class OptiCropSquare extends Internal\FilterBase implements Interfaces\IExternalFilter {
	private $gamma = 0.3;
	
	function __construct($gamma = 0.3) {
		$this->gamma = $gamma;
	}
	/**
	 * Output the data to be stored in URL
	 * @return array
	 */
	function toData() {
		return $this->gamma;
	}
	
	static function opticrop($image,$gamma) {
		// source dimensions
		$w0 = imagesx($image);
		$h0 = imagesy($image);
		
		//TODO: abstract and reove this logic
		if($w0 > $h0){
			$h = $w = $h0;
		}else{
			$h = $w = $w0;
		}
		
		// parameters for the edge-maximizing crop algorithm
		$r = 1; // radius of edge filter
		$nk = 200; // scale count: number of crop sizes to try
		$ar = $w / $h; // target aspect ratio (AR)
		$ar0 = $w0 / $h0; // target aspect ratio (AR)
		
		//EEW
		ob_start();
		imagepng($image);
		$contents = ob_get_contents();
		ob_end_clean();
		imagedestroy($image);
		$image = '/tmp/'.md5($contents).'.png';
		$out = $image.'.temp';
		file_put_contents($image, $contents);

		$img = new \Imagick ( $image );
		$imgcp = clone $img;
		
		// compute center of edginess
		$img->edgeImage ( $r );
		//$img->sharpenimage(200, 0.9);
		$img->modulateImage ( 100, 0, 100 ); // grayscale
		$img->blackThresholdImage ( "#0f0f0f" );
		$img->writeImage ( $out );
		// use gd for random pixel access
		$im = ImageCreateFromPng ( $out );
		$xcenter = 0;
		$ycenter = 0;
		$sum = 0;
		$n = 100000;
		for($k = 0; $k < $n; $k ++) {
			$i = mt_rand ( 0, $w0 - 1 );
			$j = mt_rand ( 0, $h0 - 1 );
			$val = imagecolorat ( $im, $i, $j ) & 0xFF;
			$sum += $val;
			$xcenter += ($i + 1) * $val;
			$ycenter += ($j + 1) * $val;
		}
		$xcenter /= $sum;
		$ycenter /= $sum;
		
		// crop source img to target AR
		if ($w0 / $h0 > $ar) {
			// source AR wider than target
			// crop width to target AR
			$wcrop0 = round ( $ar * $h0 );
			$hcrop0 = $h0;
		} else {
			// crop height to target AR
			$wcrop0 = $w0;
			$hcrop0 = round ( $w0 / $ar );
		}
		
		// crop parameters for all scales and translations
		$params = array ();
		
		// crop at different scales
		$hgap = $hcrop0 - $h;
		$hinc = ($nk == 1) ? 0 : $hgap / ($nk - 1);
		$wgap = $wcrop0 - $w;
		$winc = ($nk == 1) ? 0 : $wgap / ($nk - 1);
		
		// find window with highest normalized edginess
		$n = 10000;
		$maxbetanorm = 0;
		$maxfile = '';
		$maxparam = array ('w' => 0, 'h' => 0, 'x' => 0, 'y' => 0 );
		for($k = 0; $k < $nk; $k ++) {
			$hcrop = round ( $hcrop0 - $k * $hinc );
			$wcrop = round ( $wcrop0 - $k * $winc );
			$xcrop = $xcenter - $wcrop / 2;
			$ycrop = $ycenter - $hcrop / 2;
			
			if ($xcrop < 0)
				$xcrop = 0;
			if ($xcrop + $wcrop > $w0)
				$xcrop = $w0 - $wcrop;
			if ($ycrop < 0)
				$ycrop = 0;
			if ($ycrop + $hcrop > $h0)
				$ycrop = $h0 - $hcrop;
			
			$beta = 0;
			for($c = 0; $c < $n; $c ++) {
				$i = mt_rand ( 0, $wcrop - 1 );
				$j = mt_rand ( 0, $hcrop - 1 );
				$beta += imagecolorat ( $im, $xcrop + $i, $ycrop + $j ) & 0xFF;
			}
			$area = $wcrop * $hcrop;
			$betanorm = $beta / ($n * pow ( $area, $gamma - 1 ));
			// best image found, save it
			if ($betanorm > $maxbetanorm) {
				$maxbetanorm = $betanorm;
				$maxparam ['w'] = $wcrop;
				$maxparam ['h'] = $hcrop;
				$maxparam ['x'] = $xcrop;
				$maxparam ['y'] = $ycrop;
			}
		}
		// return image
		$imgcp->cropImage ( $maxparam ['w'], $maxparam ['h'], $maxparam ['x'], $maxparam ['y'] );
		$imgcp->writeImage ( $image );

		$img->destroy ();
		$imgcp->destroy ();
		
		$gd = imagecreatefromstring(file_get_contents($image));
		unlink($image);
		unlink($out);
		return $gd;
	}
	
	/**
	 * Do te work, this function is called on resource view
	 * @param resource $gd
	 * @param array $data
	 * @return resource
	 */
	static function Filter($gd, $data) {
		if (is_numeric ( $data ) && $data > 0 && $data < 1) {
			$gd = self::OptiCrop($gd,$data);
		}
		return $gd;
	}
}
