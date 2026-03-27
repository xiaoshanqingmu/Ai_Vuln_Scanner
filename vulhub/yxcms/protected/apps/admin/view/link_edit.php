<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/highslide.css" />
<script type="text/javascript" src="__PUBLIC__/js/highslide.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script  type="text/javascript" language="javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script language="javascript">
hs.graphicsDir = '__PUBLIC__/images/graphics/';
hs.wrapperClassName = 'wide-border';
hs.showCredits = false;
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
		{ name:"webname",min:3,max:30,simple:"站点名称",focusMsg:'3-30个字符'},
		{ name:"url",type:'url',simple:"站点地址",focusMsg:'请输入合法的站点地址'}
	];
	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});
  });
</script>
<title>链接编辑</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置:【链接编辑】</div>
        </div>

        <form enctype="multipart/form-data" action="{url('link/edit',array('id'=>$info['id']))}" method="post" id="info" name="info" >
         <table class="all_cont" width="100%" border="0" cellpadding="5" cellspacing="1"   > 
          <tr>
            <td align="right"  width="100">链接类型：</td>
            <td align="left"><?php if($info['type']==1) { ?>
               <input name="type" type="radio" value="1" checked="checked"/>文字链接 &nbsp;<input name="type" type="radio" value="2" />图片链接 
               <?php } else { ?>
               <input name="type" type="radio" value="1" />文字链接 &nbsp;<input name="type" type="radio" value="2" checked="checked"/>图片链接 
               <?php } ?>
            </td>
            <td class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">网站名称：</td>
            <td align="left"><input name="webname" id="webname" type="text" value="{$info['name']}" /></td>
            <td class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">链接地址：</td>
            <td align="left"><input name="url" id="url" type="text" value="{$info['url']}" /></td>
            <td class="inputhelp">格式：http://www.yxcms.net</td>
          </tr>
          <tr>
            <td align="right">上传LOGO：</td>
            <td align="left"><input type="file" name="picture" id="picture" size="10"><input type="hidden" name="oldpicture" value="{$info['picture']}"><?php if(!empty($info['picture'])) { ?>&nbsp;<a href="{$path}{$info['picture']}" onClick="return hs.expand(this)">查看logo</a><?php } ?></td>
            <td class="inputhelp">直接填写Logo地址时不用上传，重复上传之前图片将被覆盖</td>
          </tr>
          <tr>
            <td align="right">LOGO地址：</td>
            <td align="left"><input name="logourl" id="logourl" type="text" value="{$info['logourl']}" /></td>
            <td class="inputhelp">本地上传时不用填写</td>
          </tr>
          <tr>
            <td align="right">网站所有者：</td>
            <td align="left"><input name="siteowner" id="siteowner" type="text" value="{$info['siteowner']}" /></td>
            <td class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">网站简介：</td>
            <td align="left"><textarea name="info" id="info" cols="40" rows="5">{$info['info']}</textarea></td>
            <td class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">排序：</td>
            <td align="left"><input name="norder" id="norder" type="text" value="{$info['norder']}" size="6"/></td>
            <td class="inputhelp">值越大越靠前(不指定将按最新发表排序)</td>
          </tr>
          <tr>
            <td align="right">通过审核：</td>
            <td align="left">
            <?php if($info['ispass']==1) { ?>
               <input checked="checked" name="ispass"  type="radio" value="1" />是 <input name="ispass" type="radio" value="0" />否
               <?php } else { ?>
               <input name="ispass"  type="radio" value="1" />是 <input checked="checked" name="ispass" type="radio" value="0" />否
               <?php } ?>          
            </td>
            <td class="inputhelp">未通过审核的链接将不显示</td>
          </tr>   
          <tr>
            <td></td>
            <td colspan="2" align="left"><input type="submit" class="button" value="编辑">&nbsp;<input class="button" type="reset" value="重置"></td>
          </tr>           
        </table>

</form>
</div>
</body>
</html>