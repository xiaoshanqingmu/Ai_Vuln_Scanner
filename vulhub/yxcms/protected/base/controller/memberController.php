<?php
//公共类
class memberController extends baseController{
  protected $auth=array();
	public function __construct()
	{
    parent::__construct(); 
		$power=api('member','powerCheck');
    switch ($power) {
      case false://会员应用没有开启
        $this->assign('memberoff',true);
        break;
      case 1://没有权限访问
        $this->error('您没有登陆或是权限不够进入~',$_SERVER['HTTP_REFERER']);
        break;
      case 2://游客有权限访问
        break;
      default://会员信息数组,会员有权限访问
        $this->auth=$power;
        $this->assign('auth',$power);
        break;
    }
	}
}
?>