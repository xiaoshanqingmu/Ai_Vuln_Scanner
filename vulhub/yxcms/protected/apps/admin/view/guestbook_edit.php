<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script	language="javascript">
  $(function ($) { 
	//行颜色效果
	$('.all_cont tr').hover(
	function () {
        $(this).children().css('background-color', '#d5efff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	}
	);	
  });
</script>
<title>留言回复</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
		<div class="list_head_ml">当前位置：【留言回复】</div>
		<div class="list_head_mr"></div>
		</div>


		<table width="100%" border="0" cellpadding="0" cellspacing="1" class="all_cont">
			<form action="{url('guestbook/edit')}" method="post" id="info" name="info">
            <tr>
               <td align="right">留言者：</td>
               <td>{$info['name']}</td>
               <td class="inputhelp"></td>
            </tr>
            <tr>
               <td align="right">联系电话：</td>
               <td>{$info['tel']}</td>
               <td class="inputhelp"></td>
            </tr>
            <tr>
               <td align="right">QQ号码：</td>
               <td>{$info['qq']}</td>
               <td class="inputhelp"></td>
            </tr>
            <tr>
               <td align="right">IP地址：</td>
               <td>{$info['ip']}</td>
               <td class="inputhelp"></td>
            </tr>
            <tr>
               <td align="right">留言时间：</td>
               <td><?php echo date('Y-m-d h:m:s',$info['addtime']) ?></td>
               <td class="inputhelp"></td>
            </tr>
            <tr>
               <td align="right">留言内容：</td>
               <td>{$info['content']}</td>
               <td class="inputhelp"></td>
            </tr>
            <tr>
               <td align="right">回复内容：</td>
               <td><textarea name="reply" cols="50" rows="8">{$info['reply']}</textarea></td>
               <td class="inputhelp"></td>
            </tr>
            <tr>
               <td align="right">是否前台显示：</td>
               <td><?php if ($info['status']){ ?>
                     是:<input name="status" type="radio" value="1" checked="checked" />&nbsp;否:<input name="status" type="radio" value="0" />
                   <?php }else{ ?>
                   是:<input name="status" type="radio" value="1"/>&nbsp;否:<input name="status" type="radio" value="0"  checked="checked" />
                   <?php } ?>
                </td>
               <td class="inputhelp"></td>
            </tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td align="left" colspan="2"><input type="hidden" name="id" value="{$info['id']}"> <input type="submit" value="回复" class="button"></td>
			</tr>
			</form>
		</table>
</div>
</body>
</html>