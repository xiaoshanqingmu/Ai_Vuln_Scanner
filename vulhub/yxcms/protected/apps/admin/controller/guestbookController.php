<?php
class guestbookController extends commonController{
	//留言列表
	public function index()
	{
		$listRows=10;//每页显示的信息条数
		$url=url('guestbook/index',array('page'=>'{page}'));
		$limit=$this->pageLimit($url,$listRows);

		$count=model('guestbook')->count();//获取行数
		$list=model('guestbook')->select('','id,name,tel,qq,ip,addtime,status','id desc',$limit);
		$this->list=$list;
		$this->page=$this->pageShow($count);
		$this->display();
	}

	//回复留言
	public function edit()
	{
		if(!$this->isPost()){
			$id=$_GET['id'];
			if(empty($id)) $this->error('参数错误');
			$info=model('guestbook')->find("id='$id'");
			$this->info=$info;
			$this->display();
		}else{
			$id=$_POST['id'];
			$data=array();
			$data['status']=$_POST['status'];
			$data['backtime']=time();
			$data['reply']=$_POST['reply'];
			if(model('guestbook')->update("id='$id'",$data))
			$this->success('留言修改成功~');
			else $this->error('出错了~');
		}
	}

	//删除留言
	public function del()
	{
		if(!$this->isPost()){
			$id=intval($_GET['id']);
			if(empty($id)) $this->error('您没有选择~');
			if(model('guestbook')->delete("id='$id'"))
			echo 1;
			else echo '删除失败~';
		}else{
			if(empty($_POST['delid'])) $this->error('您没有选择~');
			$delid=implode(',',$_POST['delid']);
			if(model('guestbook')->delete('id in ('.$delid.')'))
			$this->error('删除成功');
		}
	}
   //留言审核,ajax
	public function lock()
	{
		$id=intval($_POST['id']);
		$lock['status']=intval($_POST['status']);
		if(model('guestbook')->update("id='{$id}'",$lock))
		echo 1;
		else echo '操作失败~';
	}

}