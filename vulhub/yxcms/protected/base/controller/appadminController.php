<?php
//公共类
class appadminController extends baseController{

	public function __construct()
	{
		if( !isset( $_SESSION )) session_start();
		$appID = config('appID');
		$this->appID = empty($appID) ? $this->appID : $appID;

		if(isset($_SESSION['admin_uid'])&&isset($_SESSION['admin_username'])&& isset($_SESSION['admin_realname'])){
		   $apppower=$_SESSION['yxapppower'];
		   if($apppower!=-1) {
			   if(!(isset($apppower[APP_NAME]) && $apppower[APP_NAME]==-1)) $this->error('您没有权限操作');
		   }
		}else $this->error('您没有登录');
		parent::__construct();
	}
}
?>