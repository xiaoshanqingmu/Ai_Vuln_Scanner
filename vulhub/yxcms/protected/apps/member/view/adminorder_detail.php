<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLIC__/admin/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script	language="javascript">
  $(function ($) { 
	//行颜色效果
	$('.all_cont tr').hover(
	function () {
        $(this).children().css('background-color', '#f4f8ff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	}
	);	
  });
</script>
<title>订单详细</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
		<div class="list_head_ml">你当前的位置：【订单详细】<a  href="javascript:history.back(-1)">【返回上一页】</a></div>
		<div class="list_head_mr">订单号：{$info['ordernum']}<font color="green">【{if $info['state']==0}未支付{elseif $info['state']==1}已支付{elseif $info['state']==2}交易成功{/if}】</font></div>
		</div>


		<table width="100%" border="0" cellpadding="0" cellspacing="1"  class="all_cont">
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
                  <td align="center">￥<?php echo number_format($val['price']*$val['num']); ?></td>
              </tr>
            {/loop}
		</table>
        
        <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#b5d6e6">
            <tr>
              <th width="80">总价：</th>
              <td><font color="#CC0000">￥{number_format($info['total'])}</font></td>
            </tr>
            <tr>
              <th>运费：</th>
              <td><font color="#CC0000">￥{number_format($info['freight'])}</font></td>
            </tr>
            <tr>
              <th>支付总额：</th>
              <td><font color="#CC0000">￥<?php echo number_format($info['total']+$info['freight']); ?></font></td>
            </tr>
            <tr><td colspan="2"><font color="green">备注信息：{$info['mess']}</font></td></tr>
        </table>
    </div>
</body>
</html>