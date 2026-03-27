<?php
class newsModel extends baseModel{
	protected $table = 'news';
	
	public function newsANDadmin($sort='',$keyword='',$limit=''){
		$where=empty($sort)?(empty($keyword)?'':' AND A.title like "%'.$keyword.'%"'):' AND A.sort like "'.$sort.'%"';
		$sql="SELECT A.id,A.sort,A.title,A.color,A.picture,A.recmd,A.hits,A.ispass,A.recmd,A.addtime,A.method,B.realname FROM {$this->prefix}news A ,{$this->prefix}admin B WHERE A.account = B.username {$where}  ORDER BY A.recmd DESC,A.norder desc,A.id DESC LIMIT {$limit}";
		return $this->model->query($sql);
	}
	public function newscount($sort='',$keyword=''){
		$where=empty($sort)?(empty($keyword)?'':'title like "%'.$keyword.'%"'):'sort like "'.$sort.'%"';
		return $this->count($where);
	}
}
?>