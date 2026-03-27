<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/highslide.css" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/uploadify/uploadify.css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script type="text/javascript" src="__PUBLICAPP__/js/farbtastic.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/highslide.js"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/kindeditor/kindeditor.js"></script>
<script type="text/javascript" src="__PUBLIC__/uploadify/jquery.uploadify-3.1.min.js"></script>
<script  type="text/javascript" language="javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script language="javascript">

KindEditor.ready(function(K) {
	K.create('#content', {
		allowPreviewEmoticons : false,
		allowImageUpload : false,
		items : [
				'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
				'insertunorderedlist', '|', 'emoticons', 'image', 'link']

	});
});
//封面图效果
hs.graphicsDir = "__PUBLIC__/images/graphics/";
hs.showCredits = false;
hs.outlineType = 'rounded-white';
hs.restoreTitle = '关闭';

function addcover(){//提取封面图事件绑定
	 $(".photo").click(function(){
		var tag=$(this).attr('id');
		$("#picture").val(tag);
		$("#cover").attr('href','{$picpath}thumb_'+tag);
	 });
}
function picdel(){//单图删除
	$('.picdel').click(function(){
		var picname=$(this).prev().val();
		var tag=$(this).parent().parent();
		$.post("{url('photo/delpic')}", { picname: picname },
				function(data){
                 alert(data);
				tag.remove();
			});
	});
}
$(function ($) { 
//标题颜色
  $('#picker').farbtastic('#color');
  $('#PickCoShow').click(function(){
	  $('#picker').toggle();
	  if(''==$('#color').val()) $('#color').val("#FFFFFF");
  });
  $('#DelColor').click(function(){
	  $('#picker').hide();
	  $('#color').val('');
	  $('#color').css('background-color','#ffffff');
  });
	//行颜色效果
	$('.all_cont tr').hover(
	function () {
        $(this).children().css('background-color', '#f4f8ff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	});
	//图片批量上传
	 $('#file_upload').uploadify({
			'auto'     : false,
			'buttonImage' : '__PUBLIC__/uploadify/downbut.jpg',
            'swf'      : '__PUBLIC__/uploadify/uploadify.swf',
            'uploader' : "{url('photo/images_upload')}",
			'onUploadSuccess' : function(file, data, response) {
                  // alert('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
				  if(data){
			      var pstr=$("#imginfo").html();
		          var itml = pstr + '<div class="photolist"><div class="pcon"><img width="{$twidth}" height="{$theight}" class="photo" id="'+data+'" src="{$picpath}thumb_'+data+'" title="点击设置为封面"></div><div class="pinfo"><input style="width:{$twidth}px" type="text" name="conlist[]"><input type="hidden" name="photolist[]" value="'+data+'"><a href="#" class="picdel"></a></div></div>';
		          $("#imginfo").html(itml);
		          addcover();
		           picdel();
				  }
             }
        });
   //表单验证
	var items_array = [
	    { name:"sort",min:6,simple:"类别",focusMsg:'选择类别'},
		{ name:"title",min:6,simple:"标题",focusMsg:'3-30个字符'},
		{ name:"method",simple:"模型/方法",focusMsg:'填写模型/方法'},
		{ name:"tpcontent",simple:"模板",focusMsg:'选择模板'}
	];

	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});
    addcover();
    picdel();
	//获取拓展字段
	ajax_fields();
  });

function ajax_fields()
 {
	var sid = $('#sort').val();
	var extfield = $('#extfield').val();//拓展表id
	var sid = sid.substring(sid.lastIndexOf(',')+1);
	$.ajax({
		type: 'POST',
		url: "{url('photo/ex_field')}",
		data: {
			sid: sid,
			extfield:extfield
		},
		dataType: "json",
		success: function(data) {
			$('#extend').html('');
			if(typeof(data[0].tableinfo)!='undefined'){
			for (var i in data) {
				var list_html = '<tr>';
				list_html += '<td width="100"  align="right" valign="middle">' + data[i].name + ':</td>';
				list_html += '<td>';
				if (data[i].type == 1) {
					list_html += '<input name="ext_' + data[i].tableinfo + '" type="text"  value="' + data[i].defvalue + '" />';
				}
				if (data[i].type == 2) {
					list_html += '<textarea name="ext_' + data[i].tableinfo + '"  cols="0"  style="width:300px !important; height:80px">' + data[i].defvalue + '</textarea>';
				}
				if (data[i].type == 3) {
					list_html += '<textarea class="excontent" name="ext_' + data[i].tableinfo + '"  cols="0"  style="width:100%;height:300px;visibility:hidden;">' + data[i].defvalue + '</textarea>';
				}
				if (data[i].type == 4) {
					list_html += '<select name="ext_' + data[i].tableinfo + '"  >';
					ary = data[i].defvalue.split("\r\n");
					var choose_value=data[i].choosevalue;//选中值
					for (var x in ary) {
						strary = ary[x].split(",");
						if(choose_value==strary[0]) var checked="selected='selected'";
						else var checked="";
						list_html += '<option '+checked+' value="' + strary[0] + '">' + strary[1] + '</option>';
					}
					list_html += '</select>';
				}
				if (data[i].type == 5) {
					list_html += '<input name="ext_' + data[i].tableinfo + '" id="ext_' + data[i].tableinfo + '" type="text"  value="' + data[i].defvalue + '" /><br>';
					list_html += '<iframe scrolling="no"; frameborder="0" src="{url("extendfield/file")}/&inputName=ext_' + data[i].tableinfo + '" style="width:300px; height:35px;"></iframe>';
				}
				if(data[i].type == 6){
					var ary = data[i].defvalue.split("\r\n");
					var choose_value=data[i].choosevalue;//选中值
					for (var x in ary) {
						var strary = ary[x].split(",");
						var valuearr = choose_value.split(",");
						for (var y in valuearr) {
						    if(valuearr[y]==strary[0]){ var checked="checked";}
						}
						list_html += strary[1] + '<input '+checked+' type="checkbox" name="ext_' + data[i].tableinfo + '[]" value="' + strary[0] + '" />';
						var checked="";
					}
				}
				list_html += '<input type="hidden" name="tableid" value="' + data[i].pid + '">';
				list_html += '</td><td></td>';
				list_html += '</tr>';
				$('#extend').append(list_html);
			}
      KindEditor.create('.excontent', {
              allowFileManager : true,
              filterMode:false,
              uploadJson : "{url('photo/UploadJson')}",
              fileManagerJson : "{url('photo/FileManagerJson')}"
         });
			}
		}
	});
}
</script>
<title>图集编辑</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">图集编辑</div>
        </div>

        <form  action="{url('photo/edit',array('id'=>$info['id']))}" method="post" id="info" onSubmit="return check_form(document.add);">
         <table class="all_cont" width="100%" border="0" cellpadding="5" cellspacing="1"   >
          <tr>
            <td align="right" width="10%">选择类别：</td>
            <td align="left" width="70%">
               <select name="sort" id="sort" onChange="ajax_fields()">
                  <option selected="selected" value="">=请选择类别=</option>
                  <?php 
                      if(!empty($sortlist)){
                      foreach($sortlist as $vo){
                          $space = str_repeat('├┈┈┈', $vo['deep']-1); 
                          $tpath=$vo['path'].','.$vo['id'];
                          $ifselect =($info['sort']==$tpath)?'selected="selected"':''; 
                          $disable=($type==$vo['type'])?'':'disabled="disabled"';  
                          $option.= '<option '.$ifselect.' value="'.$tpath.'" '.$disable.'>'.$space.$vo ['name'].'</option>';
                        }
                        echo $option;
                     }
                  ?>
               </select>
                <input type="hidden" id="oldsort" value="{$info['sort']}" name="oldsort">
            </td>
            <td class="inputhelp" width="20%"></td>
          </tr> 
          <tr>
            <td align="right">标题：</td>
            <td align="left">
               <div>
               <input type="text" name="title" id="title" value="{$info['title']}" maxlength="60" size="30" >
               <a href="javascript:" title="点击选择颜色" id="PickCoShow"><img src="__PUBLICAPP__/images/pick.gif" width="11" height="11" border="0" /></a>
               <input value="{$info['color']}" type="text" name="color" id="color" size="9">
               <a href="javascript:" title="点击清除颜色" id="DelColor"><img src="__PUBLICAPP__/images/del.gif" width="16" height="16" border="0" /></a>
              </div>
               <div id="picker"></div>
            </td>
            <td class="inputhelp">可选择前台显示的标题字体颜色</td>
          </tr>
          <tr>
            <td align="right">封面图：</td>
            <td align="left"><input type="text" readonly="readonly" value="{$info['picture']}" name="picture" id="picture" size="20" ><a id="cover" href="<?php echo $info['picture']?$picpath.'thumb_'.$info['picture']:''; ?>" onClick="return hs.expand(this)">查看封面图</a></td>
            <td class="inputhelp">若需设置请点击“上传图片管理”栏中图片</td>
          </tr>  
          <tr>
            <td align="right">SEO关键词：</td>
            <td align="left"><input type="text" value="{$info['keywords']}" name="keywords" id="keywords" size="40"></td>
            <td class="inputhelp">将被用来作为栏目页标题，用英文逗号隔开，留空时将根据标题和内容自动生成</td>
          </tr> 
          <tr>
            <td align="right">SEO描述：</td>
            <td align="left"><textarea cols="40" rows="5" name="description" id="description">{$info['description']}</textarea></td>
            <td class="inputhelp">将被用来作栏目描述，用英文逗号隔开，留空时将根据内容自动生成</td>
          </tr>
           <tr>
             <td align="right">图片上传：</td>
             <td width="50%">
               <div style="float:left; width:80%"><input type="file" name="file_upload" id="file_upload" /></div>
               <a class="upbut" href="javascript:$('#file_upload').uploadify('upload','*')">上传</a>
             </td>
             <td class="inputhelp">支持批量上传图片</td>
          </tr>
          <tr>
             <td align="right">上传图片管理：</td>
             <td id="imginfo" colspan="2">
             <?php 
               if(!empty($info['photolist'])){
                 $photoarr=explode(',',$info['photolist']);
                 $exparr=explode(',',$info['conlist']);
                 $i=0;
                 foreach($photoarr as $vo){
                     $list.='<div class="photolist">';
                     $list.='<div class="pcon"><img width="'.$twidth.'" height="'.$theight.'" class="photo" id="'.$vo.'" title="点击设置为封面" src="'.$picpath.'thumb_'.$vo.'"></div>';
                     $list.='<div class="pinfo"><input style="width:'.$twidth.'px" type="text" value="'.$exparr[$i].'" name="conlist[]"><input type="hidden" name="photolist[]" value="'.$vo.'"><a href="#" class="picdel"></a></div>';
                     $list.='</div>';
                     $i++;
                 }          
                 echo $list;
               }
             ?>
             </td>
          </tr>
          <tr>
            <td align="right">图集说明：</td>
            <td align="left"><textarea name="content" id="content" style=" width:100%;height:300px;visibility:hidden;">{$info['content']}</textarea></td>
            <td class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">前台模型/方法：</td>
            <td align="left"><input type="text" value="{$info['method']}" name="method" id="method" size="20"></td>
            <td class="inputhelp">默认为photo模型中content方法</td>
          </tr>
          <tr>
            <td align="right">前台显示模板：</td>
            <td align="left">
            <select name="tpcontent" id="tpcontent">
               {$choose}
              </select>
              </td>
            <td class="inputhelp"></td>
          </tr> 
          <tbody id="extend"></tbody>
          <tr>
            <td align="right">通过审核：</td>
            <td align="left"><input <?php echo ($info['ispass']==1)?'checked="checked"':''; ?>  name="ispass"  type="radio" value="1" />是 <input <?php echo ($info['ispass']==0)?'checked="checked"':''; ?> name="ispass" type="radio" value="0" />否</td>
            <td class="inputhelp">未通过审核的新闻将不在任何页面中显示</td>
          </tr>  
          <tr>
            <td align="right">是否推荐：</td>
            <td align="left"><input <?php echo ($info['recmd']==1)?'checked="checked"':''; ?> name="recmd"  type="radio" value="1" />是 <input <?php echo ($info['recmd']==0)?'checked="checked"':''; ?>  name="recmd" type="radio" value="0" />否</td>
            <td class="inputhelp"></td>
          </tr>
          <tr>
            <td align="right">点击次数：</td>
            <td align="left"><input name="hits" type="text" value="{$info['hits']}" size="6"/></td>
            <td class="inputhelp">不建议修改</td>
          </tr>  
          <tr>
            <td align="right">排序：</td>
            <td align="left"><input name="norder" id="norder" type="text" value="{$info['norder']}" size="6"/></td>
            <td class="inputhelp">值越大越靠前(不指定将按最新发表排序)</td>
          </tr>
          <tr>
            <td align="right">发表时间：</td>
            <td align="left"><input name="addtime" id="addtime" type="text" value="{$info['addtime']}" /></td>
            <td class="inputhelp">不建议修改</td>
          </tr> 
          <tr>
            <td><input type="hidden" id="extfield" value="{$info['extfield']}" name="extfield"></td>
            <td colspan="2" align="left"><input type="submit" class="button" value="编辑">&nbsp;<input class="button" type="reset" value="重置"></td>
          </tr>           
        </table>
</form>
</div>
</body>
</html>