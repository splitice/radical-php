<?php
namespace CLI;

declare(ticks=1); 
class Thread {
	const OPTION_DEFAULT = 0;
	const OPTION_ROUTE = -1;
	
	static $self;
	public $children = array();
	public $pid;
	function getId(){
		return $this->pid;
	}
	public $parent;
	private $name;
	public $communication;
	
	public $maxChildren = 10;
	
	private $callback = array('close'=>null);
	
	static function Init(){
		self::$self = false;//Root element
		
		self::$self = new self(null,getmypid());
		static::initSignal();
	}
	
	private static function initSignal(){
		pcntl_signal(SIGCHLD, array(self::$self, "childSignalHandler"));
		new Threading\MessageHandler();
	}
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
		if(function_exists('setproctitle')){
			global $_SCRIPT_NAME;
			setproctitle($_SCRIPT_NAME . ' ['.$name.']');
		}
	}

	public function childSignalHandler($signo, $pid=null, $status=null){
		if($signo != SIGCHLD) {
			echo "Signal\r\n";
			return true;
		}
		
        //If no pid is provided, that means we're getting the signal from the system.  Let's figure out
        //which child process ended
        //echo "Singal\r\n";
		
		if(!$pid){
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }
        
      
        //Make sure we get all of the exited children
        while($pid > 0){
        	//echo "[",getmypid(),"] Singal $pid\r\n";
        	
            if($pid && isset($this->children[$pid])){
                $exitCode = pcntl_wexitstatus($status);
                if($exitCode != 0){
                    echo "$pid exited with status ".$exitCode."\n";
                }
               	
                $this->removeChild($pid);
            }
            else if($pid){
                //Oh no, our job has finished before this parent process could even note that it had been launched!
                //Let's make note of it and handle it when the parent process is ready for it
                echo "..... Adding $pid to the signal queue ..... \n";
                	
            }
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }
        return true;
    }
    function removeChild($pid){
    	if(isset($this->children[$pid])){
	    	$this->children[$pid]->callbackClose();
	    	unset($this->children[$pid]);
    	}
    }
    function isRunning(){
    	if($this->isThis()){
    		return true;
    	}
    	if(isset(self::$self->children[$this->pid])){
    		return true;
    	}
    	return false;//TODO: Global check
    }
    function setupOutput($option){
    	switch($option){
    		case static::OPTION_ROUTE:
    			$handlers = ob_list_handlers();
    			while(ob_get_level()) ob_end_flush();
    			\Output\Filter::Register(new \Output\Filter(array('\\CLI\\Threading\\Messages\\Output','obHandler')));
    			foreach($handlers as $h){
    				ob_start($h);
    			}
    			break;
    	}
    }
	function __construct(){
		//Init
		if(self::$self === null){
			self::Init();
		}
		
		//Arguments
		$object = $pid = null;
		$options = 0;
		foreach(func_get_args() as $arg){
			if($arg instanceof Threading\Object){
				$object = $arg;
			}elseif(is_numeric($arg)){
				if($arg <= 0){
					$options = $arg;
				}else{
					$pid = $arg;
				}
			}elseif($arg != null){
				throw new \Exception('Invalid argument to CLI\\Thread');
			}
		}
		
		//What am I?		
		if($pid == null){
			$parent = self::$self;

			while(count(self::$self->children) >= self::$self->maxChildren){
				//self::$self->WaitChildren();
				pcntl_signal_dispatch();
				self::$self->Sleep(1);
			}
			
			//IForkAction
			if($object){
				$object->preFork();
			}
			
			if(\DB::$connectionPool){
				\DB::$connectionPool->CloseAll();
			}
			
			$communication = new Threading\CommunicationSet();
			
			$pid = pcntl_fork();
			
			if($pid == false){//Child
				//IForkAction
				if($object){
					$object->postFork();
				}
				
				//This is the thread
				$this->parent = $parent;
				$this->pid = getmypid();
				
				//Communication
				$this->parent->communication = $communication->getChildCommunicator();
				
				//Init
				static::initSignal();
				static::$self = $this;
				
				$this->setupOutput($options);
			}elseif($pid){
				//This is the parent
				$this->pid = $pid;
				
				//Communication
				$this->communication = $communication->getParentCommunicator();
				
				//Store as child
				self::$self->children[$pid] = $this;
			}
		}else{
			//Creating an object for use as reference -- INIT
			$this->pid = $pid;
		}
	}
	function NotImplemented(){
		throw new \Exception('Thread method not implemented for context');
		//\Output\Error::Fatal('Thread method not implemented for context',true);
	}
	function WaitChildren(){
		if($this->isThis()){
			$status = null;
			$pid = pcntl_wait($status);
			self::$self->removeChild($pid);
		}else{
			$this->NotImplemented();
		}
	}
	function isThis(){
		if($this->pid == getmypid()){
			return true;
		}
		return false;
	}
	function callbackClose($func = null){
		if($func === null){
			if($this->callback['close']){
				$this->callback['close']($this);
			}
		}else{
			$this->callback['close'] = $func;
		}
	}
	function Sleep($time){
		$time_ns = 0;
		while(is_array($time = time_nanosleep($time,$time_ns))){
			$time_ns = $time['nanoseconds'];
			$time = $time['seconds'];
			
			pcntl_signal_dispatch();
		}
		return 0;
	}
	static function hasSupport(){
		return function_exists('pcntl_fork');
	}
}