<?php
class shopcarController extends commonController
{
      public function index()
      {
        $list=get_cookie('shopcar');
        $this->list=$list;
        $this->display();
      }

      //添加商品到购物车，ajax POST方式参数code、name、price、num
	    public function caradd()
	    {
        if(!$this->isPost()) {echo '非法操作~';return;}
        $list=get_cookie('shopcar');
        $data['code']=$_POST['code'];
        $data['ordernum']='-';
        $data['name']=$_POST['name'];
        $data['price']=(float)$_POST['price'];
        $data['num']=intval($_POST['num']);
        foreach ($data as $val) { 
          if(empty($val)) {echo '商品信息不全或格式错误~';return;}
        }
        if($data['num']<=0) {echo '数量必须为正整数~';return;}
        if(!empty($list)){
          foreach ($list as $key => $value) {
            if($value['code']==$data['code']) {
              $list[$key]['num']+=$data['num'];
              set_cookie('shopcar',$list);
              echo '添加成功~';
              return;
            } 
          }
        }
        $list[]=$data;
        set_cookie('shopcar',$list);
        echo '添加成功~';
	    }
      
      //修改购物车商品数量,ajax POST方式参数code、num
      public function caredit()
      {
        if(!$this->isPost()) {echo '非法操作~';return;}
        $code=$_POST['code'];
        $num=intval($_POST['num']);
        if($num<=0) {echo '数量必须为正数~';return;}
        if(empty($code)||empty($num)) {echo '信息不完整~';return;}
        $list=get_cookie('shopcar');
        if(!empty($list)){
          foreach ($list as $key => $value) {
            if($value['code']==$code) {
              $list[$key]['num']=$num;
              set_cookie('shopcar',$list);
              echo '更新成功~';
              return;
            } 
          }
        }
        echo '该商品已不存在~';
      }
       
      //删除购物车商品,ajax
      public function cardel()
      {
         if(!$this->isPost()) {echo '非法操作~';return;}
         $list=get_cookie('shopcar');
         $code=$_POST['code'];
         if(!empty($list)){
          foreach ($list as $key => $value) {
            if($value['code']==$code) {
              unset($list[$key]);
              set_cookie('shopcar',$list);
              echo 1;
              return;
            } 
          }
        }
        echo '商品不存在~';
      }

      //清空购物车
      public function carclear()
      {
        if(set_cookie('shopcar','',time()-1)) $this->success('购物车已被清空~',$_SERVER['HTTP_REFERER']);
      }
}
?>