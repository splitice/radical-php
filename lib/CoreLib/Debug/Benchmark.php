<?php
namespace Debug;
class Benchmark {
	private $tests;
	function __construct(array $tests){
		$this->tests = $tests;
	}
	
	function benchmark($dur = 10){
		echo 'Allowing <span style="font-weight: bold;">'.$dur.'</span> seconds<br /><br />';
		$length = count($this->tests);
		foreach($this->tests as $name => $test){
			$j = 0;
			$start = microtime(TRUE);
			$end = $start + $dur;
			while(($rend = microtime(TRUE)) < $end && ++$j)
				$test();
			echo 'Test <span style="font-weight: bold;">',$name,
			'</span>: <br /><span style="margin-left: 20px;">Performed <span style="font-weight: bold;">',
			$j,'</span> tests.</span><br /><span style="margin-left: 20px;">',(int)($j/$dur),' tests per second.</span><br />',
			'<span style="margin-left: 20px;">(Took ',number_format($rend - $end, 6),
			' extra seconds to complete)</span><br /><br /><br />';
		}
	}
}