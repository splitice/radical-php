<?php
namespace CLI\Cron\Jobs;

class BackupMail extends \Core\Object implements Interfaces\ICronJob {
	static $__dependencies = array('lib.ddl.upload.module');
	static $__provides = array('lib.cli.cron.job');
	
	const FROM_EMAIL = 'no-reply@fgv.com.au';
	static $DEFAULT_CONFIG = array(
		'to'=>'mat999@gmail.com',
		'frequency'=>'weekly',
		'files'=>true,
		'host'=>'DepositFiles',
		
		'backups'=>array(
			'Database'=>array(
				'filename_format'=>'backup-database-%(day)d-%(month)d-%(year)d.sql.gz',
				'type'=>'gzip'
			),
			
		)
	);

	function getBackupConfig(){
		global $_BACKUP;
		$b = static::$DEFAULT_CONFIG;
		foreach($_BACKUP as $k=>$b){
			$b[$k] = $b;
		}
		return $b;
	}
	function getToEmail(){
		$_BACKUP = $this->getBackupConfig();
		return $_BACKUP['to'];
	}
	function getName(){
		return 'BackupMail';
	}
	function Execute(array $arguments){
		$backup = $this->getBackupConfig();
		switch($backup['frequency']){
			case 'daily':
				$do_backup = true;
				break;
			case 'monthly':
				if((int)date('d') == 1){
					$do_backup = true;
				}
				break;
			case 'weekly':
				if((int)date('w') == 0){
					$do_backup = true;
				}
				break;
		}
		if($do_backup){
			$doneBackup = $this->DoBackup($backup['files']);
			$doneBackup->Upload('\\DDL\\Hosts\\Upload\\'.$backup['host']);
			$doneBackup->Email($backup['to']);
		}
	}
	function DoBackup($files = false){
		$dBackup = new \Database\Backup\BackupAll();
		$dBackup->Execute();
	}
}