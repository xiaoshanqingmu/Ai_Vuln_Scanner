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
   $('#formid').change(function(){
		 $('#sortname').val($(this).find('option:selected').text());
	});
	//行颜色效果
	$('.all_cont tr').hover(
	function () {
        $(this).children().css('background-color', '#f4f8ff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	}
	);
	 //表单验证
	var items_array = [
		{ name:"sortname",simple:"栏目名称",focusMsg:'填写栏目名称'},
		{ name:"formid",simple:"自定义表",focusMsg:'自定义表'}
	];

	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});
  });
</script>
<title>表单栏目编辑</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【表单栏目编辑】</div>
           <div class="list_head_mr">

           </div>
        </div>

         <table width="100%" border="0" cellpadding="0" cellspacing="1"   class="all_cont">
          <form action="{url('sort/extendedit',array('id'=>$id))}"  method="post" id="info">
          <tr>
            <td align="right" width="200">所属类别：</td>
            <td align="left">
             <select name="parentid" id="parentid">
               <option value="0">=作为顶级分类=</option>
                  <?php 
                      if(!empty($list)){
                      foreach($list as $vo){
                          $space = str_repeat('├——', $vo['deep']-1);  
                          $ifselect =($oldparentid==$vo['id'])?'selected="selected"':'';
                          $option.= '<option '.$ifselect.' value="'.$vo['id'].'">'.$space.$vo['name'].'</option>';
                        }
                        echo $option;
                     }
                  ?>
             </select>
            </td>
            <td align="left" class="inputhelp">支持无限分类</td>
          </tr> 
          <tr>
            <td align="right">自定义表：</td>
            <td align="left">
              <select name="formid">
                <?php
				$formcon='';
                foreach ($forminfo as $vo) {
				  $selected=($vo['id']==$info['extendid'])?'selected="selected"':'';
				  $formcon.='<option '.$selected.' value="'.$vo['id'].'">'.$vo['name'].'</option>';
			    }
				echo $formcon;
				?>
              </select>
            </td>
            <td align="left" class="inputhelp"><a href="{url('extendfield/tableadd')}">创建自定义独立表单</a></td>
          </tr>
          <tr>
            <td align="right">自定义名称：</td>
            <td align="left">
              <input type="text" value="{$info['name']}" name="sortname" id="sortname">
            </td>
            <td align="left" class="inputhelp">请填写要添加栏目的名称</td>
          </tr> 
          <tr>
            <td align="right">SEO关键词：</td>
            <td align="left"><input value="{$info['keywords']}" type="text" name="keywords" id="keywords" size="40"></td>
            <td class="inputhelp">将被用来作为栏目页标题，用英文逗号隔开，留空时将根据标题和内容自动生成</td>
          </tr> 
          <tr>
            <td align="right">SEO描述：</td>
            <td align="left"><textarea cols="40" rows="5" name="description" id="description">{$info['description']}</textarea></td>
            <td class="inputhelp">将被用来作栏目描述，用英文逗号隔开，留空时将根据内容自动生成</td>
          </tr> 
          <tr>
            <td align="right">前台模型/方法：</td>
            <td align="left">
              <input type="text" value="{$info['method']}" name="method" id="method">
            </td>
            <td align="left" class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">前台栏目模板：</td>
            <td align="left">
              <select name="tplist" id="tplist">
               {$choose}
              </select>
            </td>
            <td align="left" class="inputhelp"></td>
          </tr> 
          <tr>
            <td align="right">前台表单记录翻页条数：</td>
            <td align="left"><input type="text" name="num" id="num" value="{$info['url']}" size="4"></td>
            <td class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">排序：</td>
            <td align="left">
              <input type="text" value="{$info['norder']}" name="norder" id="norder" value="0" size="3">
            </td>
            <td align="left" class="inputhelp">请以数字表示分类的排序（值越小越靠前）</td>
          </tr> 
          
          <tr>
            <td align="right">是否前台显示：</td>
            <td align="left"><input <?php echo ($info['ifmenu']==1)?'checked="checked"':''; ?> name="ifmenu"  type="radio" value="1" />是 <input <?php echo ($info['ifmenu']==0)?'checked="checked"':''; ?>  name="ifmenu" type="radio" value="0" />否</td>
            <td class="inputhelp">选择是否在前台各种导航菜单中显示</td>
          </tr> 
          
          <tr>
            <td width="200">&nbsp;</td>
            <td align="left" colspan="2">
              
              <input type="submit" value="编辑" class="button">
            </td>
          </tr> 
          </form>         
        </table>

</div>
</body>
</html>