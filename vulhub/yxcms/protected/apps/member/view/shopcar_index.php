<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__PUBLICAPP__/css/style.css" />
<script language="javascript">
$(function(){    
	resetnum($(".reset"));
	del($(".del"));
})  

function resetnum(obj){
	     obj.click(function(){
			var num=$(this).prev().val();
			var code=$(this).prev().attr('name');
			$.post("{url('member/shopcar/caredit')}", {code:code,num:num},
   			function(data){
				alert(data);
   			});
		});
}

function del(obj){
	 obj.click(function(){
		if(confirm('确定要删除么？')){
			var targ=$(this);
			var code=targ.parent().prev().find('input:first').attr('name');
			$.post("{url('member/shopcar/cardel')}", {code:code},
   				function(data){
					if(data==1){
					   targ.parent().parent().remove();
					}else alert(data);
   			});
		}
		});	
}
</script>
<div id="contain">
 <div class="admin_title">
   <h2>购物车</h2>
</div>
     <div class="list_t">
        <table width="100%">
            <tr>
              <th>编号</th>
              <th>名称</th>
              <th>单价</th>
              <th>数量</th>
              <th class="w100"><a href="{url('shopcar/carclear')}"  class="button">清空购物车</a></th>
            </tr>
            {if empty($list)}<tr><td colspan="5">您的购物车是空的~</td></tr>{/if}
            {loop $list $val}
              <tr>
                  <td align="center">{$val['code']}</td>
                  <td align="center">{$val['name']}</td>
                  <td align="center">￥{number_format($val['price'])}</td>
                  <td align="center">数量：<input name="{$val['code']}"  value="{$val['num']}" size="2"><a class="reset" href="javascript:void(0);">[ 更新数量 ]</a> </td>
                  <td class="w100" align="center"><a class="del" href="javascript:void(0);">[ 移除 ]</a></td>
              </tr>
            {/loop}
      <form action="{url('order/orderadd')}" method="post">
            <tr><td colspan="5">备注信息：<textarea name="mess" cols="40" rows="6"></textarea></td></tr>
        </table>
      </div>
     <div class="btn tac">
        <input type="submit" class="button" value="生成订单">
      </form>
     </div>
</div>
