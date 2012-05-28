<?php
namespace Image\Graph\Renderer;
use Image\Graph\Source\Internal\GraphBase;
use CLI\PHP\Extension;
use Image\Graph\pChart\pChart;
use Image\Graph\pChart\pData;

abstract class ImageGraph {
	const BORDER = 40;
	
	protected function _buildChart(\Image\Graph\Schema\Graph $graph){
		$ext = new Extension('gd', 'gd2');
		if(!$ext->isLoaded()){
			throw new \Exception('GD2 is required for graphing');
		}
		
		$pChart = new pChart($graph->box->width,$graph->box->height);
		
		$dataSet = new pData;
		$dataSet->SetXAxisFormat($graph->axis['X']->format);

		if($graph->axis['X']->format == 'date' && isset($graph->axis['X']->dateFormat)){
			$pChart->setDateFormat($graph->axis['X']->dateFormat);
		}
		
		$data = $graph->data->asArray();
		if($graph->type == 'pie'){
			$data = array_values($data);
		}
		
		foreach($data as $name=>$series){
			if($graph->type == 'pie')
				$name = 's'.$name;
			$dataSet->AddPoint($series,$name);
		}
		
		$dataSet->AddAllSeries();
		
		if($graph->type == 'line'){
			if(isset($graph->data['X']))
				$dataSet->SetAbsciseLabelSerie("X");
		}elseif($graph->type == 'pie'){
			if(count($data) == 2){
				$dataSet->SetAbsciseLabelSerie('s1');
			}else{
				die(var_dump($data));
				throw new \Exception('Pie graph must have two series');
			}
		}
		//die(var_dump($dataSet));
		// Initialise the graph
		if($graph->type == 'line'){
			$pChart->setFontProperties("tahoma.ttf",8);
			$pChart->setGraphArea(70,30,$graph->box->width-static::BORDER,$graph->box->height-static::BORDER);
		}
		$pChart->drawFilledRoundedRectangle(0,0,$graph->box->width,$graph->box->height,5,240,240,240);
		$pChart->drawRoundedRectangle(5,5,$graph->box->width-5,$graph->box->height-5,5,230,230,230);
		
		$pChart->drawGraphArea(255,255,255,TRUE);
		
		//Get Data
		$data = $dataSet->GetData();
		
		if($graph->type == 'line'){
			//Remove X
			if(isset($data[0]) && isset($data[0]['X'])){
				$dataSet->RemoveSerie('X');
				$data = $dataSet->GetData();
			}
			
			if($data)
				$pChart->drawScale($data,$dataSet->GetDataDescription(),pChart::SCALE_NORMAL,150,150,150,TRUE,0,2);
			$pChart->drawGrid(4,TRUE,230,230,230,50);
		}
		
		// Draw the 0 line
		$pChart->setFontProperties("tahoma.ttf",6);
		$pChart->drawTreshold(0,143,55,72,TRUE,TRUE);
		
		if($graph->title){
			$pChart->setFontProperties("tahoma.ttf",10);
		
			$size = new \Basic\String\Size($graph->title,$pChart->getFont("tahoma.ttf"));
		
			$pChart->drawTitle($size->getCenteredLeft($graph->box->width),22,$graph->title,50,50,50);
		}
		
		// Draw the line graph
		if($graph->type == 'line'){
			$pChart->drawLineGraph($data,$dataSet->GetDataDescription());
			$pChart->drawPlotGraph($data,$dataSet->GetDataDescription(),3,2,255,255,255);
		}elseif($graph->type == 'pie'){
			try {
				$radius = min($graph->box->width/2, $graph->box->height/2) - self::BORDER;
				$pChart->drawPieGraph($data, $dataSet->GetDataDescription(), $graph->box->width/2, $graph->box->height/2, $radius);
			}catch(\Exception $ex){
				die(var_dump($ex->getMessage()));
			}
		}
		
		// Finish the graph
		$pChart->setFontProperties("tahoma.ttf",8);
		$pChart->drawLegend(75,35,$dataSet->GetDataDescription(),255,255,255);

		return $pChart;
	}
}