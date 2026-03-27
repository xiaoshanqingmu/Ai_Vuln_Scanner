<?php
class extendModel extends baseModel{
	protected $table = 'extend';

	public function Extfind($table,$where){
       return $this->model->table($table)->where($where)->find();
	}

	public function Extin($table,$data){
       return $this->model->table($table)->data($data)->insert();
	}

	public function Extselect($table,$where='',$order='',$limit=''){
       return $this->model->table($table)->where($where)->order($order)->limit($limit)->select();
	}
	
	public function Extcount($table,$where=''){
	   return $this->model->table($table)->where($where)->count();
	}
}
?>