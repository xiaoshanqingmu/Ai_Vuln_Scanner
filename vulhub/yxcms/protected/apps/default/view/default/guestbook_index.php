<?php if(!defined('APP_NAME')) exit;?>
<script type="text/javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script type="text/javascript">
//菜单效果开始
$(function(){
 //表单验证
	var items_array = [
	    { name:"lname",simple:"姓名",focusMsg:'必填'},
		{ name:"tel",type:'telephone',require:false,simple:"手机号",focusMsg:'非必填'},
		{ name:"qq",type:'qq',require:false,simple:"QQ号",focusMsg:'非必填'},
		{ name:"content",simple:"留言内容",focusMsg:'必填'}
	];
	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});
});
</script>
<div id="Main">
<div class="yx-g page">
       <div class="yx-u-1-2 box">
        <div class="book-form">
          <form action="{url('guestbook/index')}" method="post" id="info" >
          <table class="form_box">
            <tr>
               <td align="right">姓名：</td>
               <td><input class="minput" type="text" name="lname" value=""  maxlength="20"></td>
            </tr>
            <tr>
               <td align="right">联系电话：</td>
               <td><input class="minput" type="text" name="tel" value=""  maxlength="20"></td>
            </tr>
            <tr>
               <td align="right">QQ号码：</td>
               <td><input class="minput" type="text" name="qq" value=""  maxlength="20"></td>
            </tr>
            <tr>
               <td align="right">留言内容：</td>
               <td><textarea class="minput"  name="content" cols="30" rows="4"></textarea  ></td>
            </tr>
			<tr>
				<td width="100"></td>
				<td align="left" colspan="2"> <input type="submit" value="留 言" class="yx-button"></td>
			</tr>
          </table>
	      </form>
          </div>
       </div>
       <div class="yx-u-1-2">
          <div class="book-list box">
            <ul>         
            {loop $list $vo}
               <li>
                  <div class="book-list-info">留言者：{$vo['name']}&nbsp;&nbsp;&nbsp;  IP:{$vo['ip']} &nbsp;&nbsp;&nbsp; 留言时间:{date($vo['addtime'],Y-m-d H:m:i)}</div>
                  <div class="book-list-con">{$vo['content']}</div>
                  <div class="book-list-back">{$vo['reply']}</div>
               </li>
            {/loop}
           </ul>
           <div class="pagelist yx-u">{$page}</div>
           </div>
       </div>
</div>

</div> 