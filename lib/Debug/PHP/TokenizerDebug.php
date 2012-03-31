<?php
namespace Debug\PHP;

class TokenizerDebug {
	function __construct($source){
		$this->tokens = token_get_all($source);
	}
	function Output(){
		echo '<table>';
		echo '<tr><th>Token</th><th>Text</th></tr>';
		foreach($this->tokens as $t){
			$tt = $t[0];
			if(is_numeric($tt)){
				$tt = token_name($tt);
			}else{
				$t[1] = $tt;
			}
			echo '<tr><td>'.$tt.'</td><td>',$t[1],'</td></tr>';
		}
		echo '</table>';
	}
}