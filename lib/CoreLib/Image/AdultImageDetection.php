<?php
namespace Image;
class AdultImageDetection
{                              #R  G  B
    const colorA = 7944996;     #79 3B 24
    const colorB = 16696767;    #FE C5 BF
    
    const NEW_SIZE = 200;
    const MAX_AREA = 15000;
    
    private static function Resize($img,$x,$y){
    	if($x>$y){
    		$width = self::NEW_SIZE;
    		$height = floor($y*self::NEW_SIZE/$x);
    	}else{
    		$height = self::NEW_SIZE;
    		$width = floor($x*self::NEW_SIZE/$y);
    	}
    	
    	$im2 = imagecreatetruecolor($width, $height);
    	imagecopyresampled($im2, $img, 0, 0, 0, 0, $width, $height, $x, $y);
    	
    	return array($width,$height,$im2);
    }
    
    static function GetScore($img,$x,$y)
    {
    	static $arA = -1;
    	static $arB = -1;
    	if($arA == -1){
    		$arA = $arB = array();
	        $arA[0] = (self::colorA >> 8) & 0xFF;
	        $arA[1] = self::colorA & 0xFF;
	        
	        $arB[0] = (self::colorB >> 8) & 0xFF;
	        $arB[1] = self::colorB & 0xFF;
    	}
    	
    	$free = false;
    	if(($x * $y) > self::MAX_AREA){
    		list($x,$y,$img) = self::Resize($img,$x,$y);
    		$free = true;
    	}
    	
        $score = 0;
        
        $xPoints = array($x/8, $x/4, ($x/8 + $x/4), $x-($x/8 + $x/4), $x-($x/4), $x-($x/8));
        $yPoints = array($y/8, $y/4, ($y/8 + $y/4), $y-($y/8 + $y/4), $y-($y/8), $y-($y/8));
        $zPoints = array($xPoints[2], $yPoints[1], $xPoints[3], $y);

        
        for($i=0; $i<$x; ++$i)
        {
            for($j=0; $j<$y; ++$j)
            {
                $color = imagecolorat($img, $i, $j);
                if($color >= self::colorA && $color <= self::colorB)
                {
                	$g = ($color >> 8) & 0xFF;
                	$b = $color & 0xFF;
                    if($b >= $arA[1] && $b <= $arB[1] && $g >= $arA[0] && $g <= $arB[0])
                    {
                        if($i >= $zPoints[0] && $j >= $zPoints[1] && $i <= $zPoints[2] && $j <= $zPoints[3])
                        {
                            $score += 300;
                        }
                        elseif($i <= $xPoints[0] || $i >=$xPoints[5] || $j <= $yPoints[0] || $j >= $yPoints[5])
                        {
                            $score += 10;
                        }
                        elseif($i <= $xPoints[0] || $i >=$xPoints[4] || $j <= $yPoints[0] || $j >= $yPoints[4])
                        {
                            $score += 40;
                        }
                        else
                        {
                            $score += 150;
                        }
                    }
                }
            }
        }
        
        if($free){
        	imagedestroy($img);
        }
        
        $score = $score / ($x * $y);
        if($score > 100) $score = 100;
        return $score;
    }
}