<?php
class extendModel extends baseModel{
	protected $table = 'extend';

	public function addtable($table,$type=0){
	   if($type)
       $sql="CREATE TABLE IF NOT EXISTS {$this->prefix}{$table}
			(
                `id` int(11) NOT NULL auto_increment,
                `addtime` int(11) NOT NULL,
                `ip` varchar(16) NOT NULL,
                `ispass` tinyint(1) NOT NULL,
                 PRIMARY KEY  (`id`)
            )";
       else $sql="CREATE TABLE IF NOT EXISTS {$this->prefix}{$table}
			(
                `id` int(11) NOT NULL auto_increment,
                 PRIMARY KEY  (`id`)
            )";
       return $this->model->query($sql);
	}

	public function deltable($table){
	   $sql="DROP TABLE {$this->prefix}{$table}";
       return $this->model->query($sql);
	}

	public function addfield($table,$field,$type){
	   $sql="alter table {$this->prefix}{$table} ADD `{$field}` {$type} NOT NULL ";
       return $this->model->query($sql);
	}

	public function delfield($table,$field){
	   $sql="alter table {$this->prefix}{$table} DROP COLUMN {$field}";
       return $this->model->query($sql);
	}

	public function Extfind($table,$where=''){
       return $this->model->table($table)->where($where)->find();
	}

	public function Extselect($table,$where='',$order='',$limit=''){
       return $this->model->table($table)->where($where)->order($order)->limit($limit)->select();
	}

	public function Extin($table,$data){
       return $this->model->table($table)->data($data)->insert();
	}

	public function Extup($table,$where,$data){
       return $this->model->table($table)->where($where)->data($data)->update();
	}

	public function Extdel($table,$where){
       return $this->model->table($table)->where($where)->delete();
	}

	public function Extcount($table,$where=''){
	   return $this->model->table($table)->where($where)->count();
	}
}
?>