<?php
namespace Web\Pages;
use Database\Model\TableReferenceInstance;

use Web\Session\User\IUserAdmin;

use Net\URL\Pagination\QueryMethod;

use Web\Pages\Special\Redirect;

use Image\Graph\Renderer\Base64String;
use FGV\DB\Block;
use Web\PageHandler;

class Admin extends PageHandler\HTMLPageBase {
	protected $table;
	protected $action = 'list';
		
	function __construct($data){
		if(!(\Web\Session::$data['user'] instanceof IUserAdmin)){
			exit;
		}
		if(isset($data['class'])) {
			$this->table = new TableReferenceInstance(\ClassLoader::getProjectSpace('DB\\'.$data['class']));
			$this->action = isset($_REQUEST['action'])?$_REQUEST['action']:'view';
		}
	}
	function POST(){
		switch($this->action){
			case 'edit':
				$data = array();
				foreach($_POST as $k=>$v){
					if(is_array($v)){
						foreach($v as $vk=>$vv){
							//TODO: multi support
							while(is_array($vv)){
								$vv = array_pop($vv);
							}
							$data[$vk][$k] = $vv;
						}
					}
				}
				
				$class = $this->table->getClass();
				
				foreach($data as $id=>$d){
					$id = unserialize($_GET['id']);
					$o = $class::fromSQL($id,true);
					foreach($d as $k=>$v){
						$o->setSQLField($k,$v);
					}
					$o->Update();
				}
				
				return new Redirect($this->table->getName());
				//return $this->GET('Updated');
				break;
				
			case 'add':
				$class = $this->table->getClass();
				$o = $class::fromSQL($_POST,true);
				$o->Insert();
				return new Redirect($this->table->getName());
				
		}
		return $this->GET();
	}
	function GET(){
		if($this->action == 'list'){
			$classes = \ClassLoader::getNSExpression(\ClassLoader::getProjectSpace('DB\\*'));
			$prefix = \ClassLoader::getProjectSpace('DB\\');
			foreach($classes as $k=>$v){
				$classes[$k] = substr($v,strlen($prefix));
			}
				
			
			$vars = array();
			$vars['classes'] = $classes;
				
			return new \Web\Template('admin_table_list',$vars);
		}else{
			switch($this->action){
				case 'delete':
					$id = unserialize($_GET['id']);
					$class = $this->table->getClass();
					$obj = $class::fromId($id);
					$obj->Delete();
					return new Redirect($this->table->getName());
				case 'add':
					$_GET['id'] = null;
				case 'edit':
					$tm = new \HTML\Form\Builder\FormInstance($this->table);
					$id = unserialize($_GET['id']);
					$form = $tm->fromId($id);
					return new \Web\Template('admin_edit_single',array('form'=>$form,'relations'=>$this->table->getTableManagement()->getRelations()));
				case 'edit_all':
					$tm = new \HTML\Form\Builder\Adapter\DatabaseTable($this->table);
					$form = $tm->getAll();
					echo $form->toHTML();
					break;
				case 'view':
					$vars = array();
					$class = $this->table->getClass();
					$per_page = 30;
					$vars['count']  = ceil($class::getAll()->getCount()/$per_page);
					
					$pagination = new \Net\URL\Pagination\QueryMethod();
					$vars['pagination'] = $pagination;
					
					$vars['data'] = $class::getAll($pagination->getLimit($per_page));
					//Get Columns
					$tableManagement = $this->table->getTableManagement();
					$vars['cols'] = $tableManagement->getColumns();
					
					return new \Web\Template('admin_table_view',$vars);
					break;
			}
		}
	}
}