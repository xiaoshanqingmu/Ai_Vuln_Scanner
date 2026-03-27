<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script type="text/javascript" src="__PUBLICAPP__/js/farbtastic.js"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/kindeditor/kindeditor.js"></script>
<script  type="text/javascript" language="javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/jquery.imgareaselect.min.js"></script><!--剪切插件-->
<script language="javascript">
var imgcover=new Object(); //图片剪切对象
//编辑器
KindEditor.ready(function(K) {
	K.create('#content', {
		allowFileManager : true,
		filterMode:false,
		uploadJson : "{url('news/UploadJson')}",
		fileManagerJson : "{url('news/FileManagerJson')}"
	});
});
//封面处理
function covershow(){
	$('#covershow').click(function(){
		$("#coverimg").attr("src","{$path}{$info['picture']}?"+(new Date()).getTime());
		$('.arcover').show();
		$(this).unbind("click");
		$(this).html('收起封面编辑');
		$(this).attr('id','coverhide');
		coverhide();
	});
}
function coverhide(){
	$('#coverhide').click(function(){
		imgcover.cancelSelection();
		$('.arcover').hide();
		$(this).unbind("click");
		$(this).html('查看编辑封面');
		$(this).attr('id','covershow');
		covershow();
	});
}
function sizschange(width,height){
	imgcover.cancelSelection();
	imgcover=$("#coverimg").imgAreaSelect({ aspectRatio: width+':'+height,onSelectChange: preview,instance: true}); //图片剪切框效果加
}
function preview(img, selection) { //剪切区域改变触发函数
	  $('#x1').val(selection.x1);
	  $('#y1').val(selection.y1);
	  $('#w').val(selection.width);
	  $('#h').val(selection.height);
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
	
   //表单验证
	var items_array = [
	    { name:"sort",min:6,simple:"类别",focusMsg:'选择类别'},
		{ name:"title",min:2,simple:"标题",focusMsg:'3-30个字符'},
		{ name:"method",simple:"模型/方法",focusMsg:'填写模型/方法'},
		{ name:"tpcontent",simple:"模板",focusMsg:'选择模板'}
	];

	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});
	//获取拓展字段
	ajax_fields();
	//封面图剪切处理
	imgcover = $("#coverimg").imgAreaSelect({ aspectRatio: '{$twidth}:{$theight}',onSelectChange: preview,instance: true}); //图片剪切框效果加载,aspectRatio为选择框长宽
	covershow();
	coverhide();
	$("#resize").click(function(){//设置比例
			var width=$("#thumb_w").val();
			var height=$("#thumb_h").val();
			sizschange(width,height);				
	});
	$("#cut").click(function(){//剪切图片
		var x1 = $('#x1').val(); var y1 = $('#y1').val(); var w = $('#w').val(); var h = $('#h').val();var thumb_w=$('#thumb_w').val(); var name="{$info['picture']}";
		if(x1=="" || y1=="" || w=="" || h==""){
			alert("您必须先选择剪切区域~");
			return false;
		}
		$.post("{url('news/cutcover')}", { name: name, x1: x1, y1: y1, w: w, h: h,thumb_w: thumb_w}, 
			   function(data){
				   if(data){
					$("#coverimg").hide();
					$("#coverimg").attr("src","{$path}"+data+"?"+(new Date()).getTime());
					$("#coverimg").show();
					$("#setinfo").hide();
					imgcover.cancelSelection();
					imgcover.setOptions({disable:true});
					$('#cut,.imgareaselect-outer,.imgareaselect-border1,.imgareaselect-border2,.imgareaselect-border3,.imgareaselect-border4,.imgareaselect-handle').remove();
				   }else alert('剪切失败,请检查是否是图片太大超出屏幕,或者是您使用了非主流浏览器~');
			  });
	});
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
              uploadJson : "{url('news/UploadJson')}",
              fileManagerJson : "{url('news/FileManagerJson')}"
           });
			}
		}
	});
}
</script>
<title>资讯信息编辑</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：资讯编辑</div>
        </div>

        <form enctype="multipart/form-data" action="{url('news/edit',array('id' => $info['id']))}" method="post" id="info" >
         <table class="all_cont" width="100%" border="0" cellpadding="5" cellspacing="1"   >
          <tr>
            <td align="right" width="100">选择类别：</td>
            <td align="left">
               <select name="sort" id="sort" onChange="ajax_fields()">
                  <option selected="selected" value="">=请选择类别=</option>
                  <?php 
                      if(!empty($sortlist)){
                      foreach($sortlist as $vo){
                          $space = str_repeat('├┈┈┈', $vo['deep']-1);
                          $disable=($type==$vo['type'])?'':'disabled="disabled"'; 
                          $tpath=$vo['path'].','.$vo['id'];
                          $ifselect =($info['sort']==$tpath)?'selected="selected"':'';   
                          $option.= '<option '.$ifselect.' value="'.$tpath.'"'.$disable.'>'.$space.$vo['name'].'</option>';
                        }
                        echo $option;
                     }
                  ?>
               </select>
               <input type="hidden" id="oldsort" value="{$info['sort']}" name="oldsort">
            </td>
            <td class="inputhelp"></td>
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
            <td align="left">
            <input type="file" name="picture" id="picture" size="10"><input type="hidden" name="oldpicture" value="{$info['picture']}">
            <?php if(!empty($info['picture']) && $info['picture']!='NoPic.gif'){ ?>
                <a href="#" id="covershow">查看编辑封面</a>
                <div class="arcover">
                  <div style="clear:both; z-index:10000; margin-bottom:20px" id="setinfo">
                     宽：<input type="text" name="thumb_w" value="{$twidth}" id="thumb_w" size="4"/>px&nbsp;&nbsp;&nbsp;&nbsp;
                     高：<input type="text" name="thumb_h" value="{$theight}" id="thumb_h" size="4"/>px
                     <input type="button" id="resize" class="button" value="重设">&nbsp;&nbsp;&nbsp;&nbsp;
                     <input type="button" title="剪切" value="" id="cut" />  
                   </div>
                   <div style="clear:both">
                     <img id="coverimg" src="{$path}{$info['picture']}" border="0">
                     
                   </div>  
                </div>
            
               <input type="hidden" name="x1" value="" id="x1" />
	          <input type="hidden" name="y1" value="" id="y1" />
	          <input type="hidden" name="w" value="" id="w" />
	          <input type="hidden" name="h" value="" id="h" />
            <?php } ?>
            
            
              
            </td>
            <td class="inputhelp">若不手动添加，则自动提取内容中第一张图片</td>
          </tr> 
          <tr>
            <td align="right">新闻来源：</td>
            <td align="left"><input type="text" value="{$info['from']}" name="from" id="from" size="20"></td>
            <td class="inputhelp">若是转载内容，请在此注明，以避免知识产权纠纷</td>
          </tr>  
          <tr>
            <td align="right">SEO关键词：</td>
            <td align="left"><input type="text" value="{$info['keywords']}" name="keywords" id="keywords" size="40"></td>
            <td class="inputhelp">将被用来作为页面标题，用英文逗号隔开，留空时将根据摘要跟标题自动生成关键词</td>
          </tr> 
          <tr>
            <td align="right">SEO描述：</td>
            <td align="left"><textarea cols="70" rows="5" name="description" id="description">{$info['description']}</textarea></td>
            <td class="inputhelp">将被用来作页面描述，留空时将根据内容自动生成</td>
          </tr>
          <tr>
            <td align="right">内容：</td>
            <td align="left" colspan="2"><textarea name="content" id="content" style=" width:100%;height:450px;visibility:hidden;">{$info['content']}</textarea></td>
          </tr>
          <tr>
            <td align="right">前台模型/方法：</td>
            <td align="left"><input type="text" value="{$info['method']}" name="method" id="method" size="20"></td>
            <td class="inputhelp">默认为news模型中content方法</td>
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