<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__PUBLICAPP__/css/style.css" />
<div id="contain">
<div class="admin_title">
   <h2>订单号：{$info['ordernum']}<font color="green">【{if $info['state']==0}未支付{elseif $info['state']==1}已支付{elseif $info['state']==2}交易成功{/if}】</font></h2>
   <a class="right button" href="javascript:history.back(-1)">返回上一页</a>
</div>
     <div class="list_t">
        <table width="100%">
            <tr>
              <th>编号</th>
              <th>名称</th>
              <th>数量</th>
              <th>单价</th>
              <th>总价</th>
            </tr>
            {loop $list $val}
              <tr>
                  <td align="center">{$val['code']}</td>
                  <td align="center">{$val['name']}</td>
                  <td align="center">{$val['num']}</td>
                  <td align="center">￥{number_format($val['price'])}</td>
                  <td align="center">￥<?php echo number_format($val['price']*intval($val['num'])); ?></td>
              </tr>
            {/loop}
        </table>
        <table width="100%">
            <tr>
              <th width="80">总价：</th>
              <td><font color="#CC0000">￥{number_format($info['total'])}</font></td>
            </tr>
            <tr>
              <th>运费：</th>
              <td><font color="#CC0000">￥{$info['freight']}</font></td>
            </tr>
            <tr>
              <th>支付总额：</th>
              <td><font color="#CC0000">￥<?php echo number_format($info['total']+$info['freight']); ?></font></td>
            </tr>
            <tr><td colspan="2"><font color="green">备注信息：{$info['mess']}</font></td></tr>
        </table>
      </div>
     <div class="tac">
     {if $info['state']==0}<a class="button" href="{url('order/pay',array('id'=>$info['id']))}">立即购买</a>{elseif $info['state']==1}<font color="green"><a class="button" href="{url('order/sure',array('id'=>$info['id']))}">确认收货</a>如果您注册后没有完善您的联系方式，请直接联系我们的客服，领取您购买的商品或服务</font>{/if}
     {if $info['state']!=1}<a class="button" href="{url('order/del',array('id'=>$info['id']))}">删除订单</a>{/if}
        
     </div>
</div>
