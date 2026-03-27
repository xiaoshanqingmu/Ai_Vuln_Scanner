<?php
class orderController extends commonController
{
      public function index()
      {
        $account = $this->auth['account'];

        $listRows=10;//每页显示的信息条数
        $url=url('order/index',array('page'=>'{page}'));
        $limit=$this->pageLimit($url,$listRows);
        $where="account='{$account}'";
        $count=model('orders')->count($where);

        
        $list=model('orders')->select($where,'','id DESC',$limit);
        $this->list=$list;
        $this->page=$this->pageShow($count);
        $this->display();
      }

      public function orderadd()
      {
        $list=get_cookie('shopcar');
        $account = $this->auth['account'];
        if(empty($account)) $this->error('您还没有登录~');
        if(!empty($list)){
          $data['ordernum']=date("YmdHis").rand(0,100);
          $data['account']=$account;
          $data['freight']=empty($_POST['freight'])?0:intval($_POST['freight']);//运费
          $data['total']=$data['freight'];
          $data['ordertime']=time();
          $data['state']=0;
          $data['mess']=trim(in($_POST['mess']));
          
          foreach ($list as $value) {
             $value['ordernum']=$data['ordernum'];
             $id=model('orderDetail')->insert($value);
             if(!$id) $this->error('订单物品信息有误~');
             $data['total']+=intval($value['price'])*intval($value['num']);
          }
          if(model('orders')->insert($data)) {
            set_cookie('shopcar','',time()-1);
            $this->success('订单已生成~',url('order/index'));
          }
          else $this->error('订单生成失败~');
        }
      }

      public function detail()
      {
          if(empty($_GET['id'])) $this->error('非法操作~');
          $id=intval($_GET['id']);
          $account = $this->auth['account'];
          if(empty($account)) $this->error('您还没有登录~');
          $info=model('orders')->find("id='{$id}' AND account='{$account}'");
          $list=model('orderDetail')->select("ordernum={$info['ordernum']}",'','id DESC');

          $this->info=$info;
          $this->list=$list;
          $this->display();
      }
      public function pay()
      {
          if(empty($_GET['id'])) $this->error('非法操作~');
          $id=intval($_GET['id']);
          $account = $this->auth['account'];
          if(empty($account)) $this->error('您还没有登录~');
          $info=model('orders')->find("id='{$id}' AND account='{$account}' AND state='0'",'total');
          if(empty($info)) $this->error('该订单不存在~');
          $mes=model('members')->find("account='{$account}'","rmb,crmb");
          if(($mes['rmb']-$mes['crmb']-$info['total'])<0) $this->error('您的额只有￥'.($mes['rmb']-$mes['crmb']).'了，请先联系客服充值~');
          $mes['crmb']+=$info['total'];
          if(!model('members')->update("account='{$account}'",$mes)) $this->error('更新虚拟币失败~');
          if(!model('orders')->update("id='{$id}' AND account='{$account}' AND state='0'","state='1'")) $this->error('更新订单状态失败~');
          $this->success('支付成功~',url('order/index'));
      }
      public function del()
      {
          if(empty($_GET['id'])) $this->error('非法操作~');
          $id=intval($_GET['id']);
          $account = $this->auth['account'];
          if(empty($account)) $this->error('您还没有登录~');
          $info=model('orders')->find("id='{$id}' AND account='{$account}' AND state!='1'",'ordernum');
          if(empty($info)) $this->error('该订单未交易完成~');
          if(!model('orderDetail')->delete("ordernum='{$info['ordernum']}'")) $this->error('订单详细删除失败~');
          if(!model('orders')->delete("account='{$account}' AND id='{$id}'")) $this->error('订单删除失败~');
          $this->success('订单删除成功~',url('order/index'));
      }
      public function sure()
      {
          if(empty($_GET['id'])) $this->error('非法操作~');
          $id=intval($_GET['id']);
          $account = $this->auth['account'];
          if(empty($account)) $this->error('您还没有登录~');
          if(!model('orders')->update("id='{$id}' AND account='{$account}' AND state='1'","state='2'")) $this->error('订单确认失败~');
          $this->success('订单确认成功~',url('order/index'));
      }
}
?>