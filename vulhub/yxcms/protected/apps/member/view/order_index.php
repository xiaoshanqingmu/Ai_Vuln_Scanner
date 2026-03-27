<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__PUBLICAPP__/css/style.css" />
<div id="contain">
 <div class="admin_title">
   <h2>订单管理</h2>
   </div>
     <div class="list_t">
        <table width="100%">
            <tr>
              <th>订单号</th>
              <th>总价</th>
              <th>下单时间</th>
              <th>状态</th>
              <th></th>
            </tr>
            {if empty($list)}<tr><td colspan="5">您还没有订单~</td></tr>{/if}
            {loop $list $val}
              <tr>
                  <td align="center">{$val['ordernum']}</td>
                  <td align="center"><font color="#CC0000">￥{number_format($val['total'])}</font></td>
                  <td align="center">{date($val['ordertime'],Y-m-d H:i:s)}</td>
                  <td align="center"><font color="green">{if $val['state']==0}未支付{elseif $val['state']==1}已支付{elseif $val['state']==2}交易成功{/if}</font></td>
                  <td class="w100" align="center"><a href="{url('order/detail',array('id'=>$val['id']))}">[ 详细 ]</a>{if $val['state']!=1}<a href="{url('order/del',array('id'=>$val['id']))}">[ 删除 ]</a>{/if}</td>
              </tr>
            {/loop}
        </table>
      </div>
      <div class="tac">
         {$page}
     </div>
</div>
