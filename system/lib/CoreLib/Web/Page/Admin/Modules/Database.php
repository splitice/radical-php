<?php
namespace Web\Page\Admin\Modules;

use Web\Page\Admin\AdminModuleBase;
use Model\Database\Model\TableReferenceInstance;
use Web\Session\User\IUserAdmin;
use Net\URL\Pagination\QueryMethod;
use Web\Page\Controller\Special\Redirect;
use Web\Templates;
use Web\Form;

class Database extends AdminModuleBase {
	protected $table;
	protected $action = 'list';
		
	function __construct(\Net\URL\Path $url = null){
		if($url){
			$class = $url->firstPathElement();
			if($class) {
				$model = \Core\Libraries::getProjectSpace('DB\\'.$class);
				if(!class_exists($model)){
					throw new \Exception('Cant find database model of type '.$class);
				}
				
				$this->table = new TableReferenceInstance($model);
				$this->action = isset($_REQUEST['action'])?$_REQUEST['action']:'view';
			}
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
	private static function extractName($name){
		$prefix = \Core\Libraries::getProjectSpace('DB\\');
		return substr($name,strlen($prefix));
	}
	function GET(){
		if($this->action == 'list'){
			$classes = array();
			foreach(\Core\Libraries::getNSExpression(\Core\Libraries::getProjectSpace('DB\\*')) as $k=>$v){
				$name = self::extractName($v);
				$classes[$name] = self::toURL().'/'.$name;
			}
				
			
			$vars = array();
			$vars['classes'] = $classes;
				
			return new Templates\ContainerTemplate('Database/admin_table_list',$vars,'admin');
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
					$vars = array('form'=>$form,'relations'=>$this->table->getTableManagement()->getRelations());
					return new Templates\ContainerTemplate('Database/admin_edit_single',$vars,'admin');
				case 'edit_all':
					$tm = new Form\Builder\Adapter\DatabaseTable($this->table);
					$form = $tm->getAll();
					echo $form->toHTML();
					break;
				case 'view':
					$vars = array();
					$per_page = 30;
					$vars['count']  = ceil($this->table->getAll()->getCount()/$per_page);
					
					$pagination = new \Net\URL\Pagination\QueryMethod();
					$vars['pagination'] = $pagination;
					
					$vars['data'] = $this->table->getAll($pagination->getLimit($per_page));

					//Get Columns
					$tableManagement = $this->table->getTableManagement();
					$vars['cols'] = $tableManagement->getColumns();
					
					return new Templates\ContainerTemplate('Database/admin_table_view',$vars,'admin');
					break;
			}
		}
	}
	
	function toURL(){
		$url = rtrim(parent::toURL(),'/');
		if($this->table){
			$url .= '/'.self::extractName($this->table->getClass());
		}
		return $url;
	}
}