<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script  type="text/javascript" language="javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/kindeditor/kindeditor.js"></script>
<script language="javascript">
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
  function getcon() {
	  $('#sortcon').html('<div id="loading"></div>');
	  var sortaction=$('#colsort').val();
      $.get("{url('sort/add')}", {sortaction : sortaction},
   		 function(data){
		   $('#sortcon').html(data);
		   if(sortaction=='pageadd'){
                KindEditor.create('#content', {
		            allowFileManager : true,
		            filterMode:false,
		            uploadJson : "{url('sort/PageUploadJson')}",
		            fileManagerJson : "{url('sort/PageFileManagerJson')}"
	            });
		   }
   	    });
  }
</script>
<title>添加栏目</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【添加栏目】</div>
           <div class="list_head_mr">

           </div>
</div>
    <table width="100%" border="0" cellpadding="0" cellspacing="1"   class="all_cont">       
          <tr>
            <th>
             <select id="colsort" onChange="getcon()">
               <option selected="selected" value="noadd" >=选择栏目类型=</option>
               <option value="newsadd" >资讯栏目</option>
               <option value="photoadd" >图集栏目</option>
               <option value="pageadd" >单页栏目</option>
               <option value="pluginadd" >应用栏目</option>
               <option value="extendadd" >表单栏目</option>
               <option value="linkadd" >自定义栏目</option>
             </select>
            </th>
          </tr>
    </table>
    <div id="sortcon"></div>

</div>
</body>
</html>