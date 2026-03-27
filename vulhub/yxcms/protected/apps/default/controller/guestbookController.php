<?php
class guestbookController extends commonController{
	public function index()
    {
      if(!$this->isPost()){
		 $listRows=5;//每页显示的信息条数
		 $url=url('guestbook/index',array('page'=>'{page}'));
		 $limit=$this->pageLimit($url,$listRows);

         $count=model('guestbook')->count("status='1'");
         $list=model('guestbook')->select("status='1'","name,ip,content,reply,addtime",'id DESC',$limit);

         $this->list=$list;
         $this->sorts=$this->sorts;
         $this->page=$this->pageShow($count);
         $this->display();
       }
      else{
         if(empty($_POST['lname'])||empty($_POST['content'])) $this->error('姓名和内容必填~');
         $data=array();
         $data['content']=in($_POST['content']);
         $data['name']=in($_POST['lname']);
         $data['tel']=in($_POST['tel']);
         $data['qq']=in($_POST['qq']);
         $data['addtime']=time();
         $data['ip']=get_client_ip();
         $data['status']=0;
         if(model('guestbook')->insert($data))
            $this->success('留言成功~',url('index/index'));
         else $this->error('留言失败!');
      }
    }
}