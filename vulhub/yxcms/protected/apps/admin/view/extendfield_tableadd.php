<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script  type="text/javascript" language="javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script language="javascript">
  $(function ($) { 
	//行颜色效果
	$('.all_cont tr').hover(
	function () {
        $(this).children().css('background-color', '#f4f8ff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	});
   //表单验证
	var items_array = [
		{ name:"tname",simple:"用途描述",focusMsg:''},
		{ name:"tableinfo",type:"eng",min:3,simple:"拓展表名",focusMsg:'必须是英文字符'}
	];

	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});
  });
</script>
<title>拓展表添加</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【拓展表添加】</div>
        </div>

        <form  action="{url('extendfield/tableadd')}"  method="post" id="info" name="info" >
         <table class="all_cont" width="100%" border="0" cellpadding="5" cellspacing="1"   > 
          <tr>
            <td align="right">类型：</td>
            <td align="left">
                <select name="type">
                   <option selected value="0">附属表</option>
                   <option value="1">独立表单</option>
                </select>
            </td>
            <td class="inputhelp">附属表用于资讯和图集的字段拓展<br>独立表用于自定义表单</td>
          </tr>
          <tr>
            <td align="right" width="200">用途描述：</td>
            <td align="left"><input name="tname" id="tname" type="text" value="" /></td>
            <td class="inputhelp"></td>
          </tr>
          
          <tr>
            <td align="right">表名：</td>
            <td align="left"><input name="tableinfo" id="tableinfo" type="text" value="" /></td>
            <td class="inputhelp">数据库拓展表名</td>
          </tr>
          <tr>
            <td></td>
            <td colspan="2" align="left"><input type="submit" class="button" value="添加"></td>
          </tr>           
        </table>
</form>
</div>
</body>
</html>
