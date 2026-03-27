<?php if(!defined('APP_NAME')) exit;?>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/kindeditor/kindeditor.js"></script>
<script type="text/javascript">
KindEditor.ready(function(K) {
	K.create('.editori', {
		allowPreviewEmoticons : false,
		allowImageUpload : false,
		items : [
				'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
				'insertunorderedlist', '|', 'emoticons', 'image', 'link']

	});
});
//重载验证码
function fleshVerify()
{
var timenow = new Date().getTime();
document.getElementById('verifyImg').src= "{url('index/verify')}/"+timenow;
}
</script>
<div id="Main">
<div class="page">
        <div class="book-form box">
        <div class="bock-tit"><h3>当前位置：{$daohang}</h2></div>
          <form action="{url('extend/index',array('id'=>$id))}" method="post" id="info" >
          <table class="form_box">
       {for $i=1;$i<count($tableinfo);$i++}
             <tr><td align="right" width="150">{$tableinfo[$i]['name']}：</td><td align="left">
             {if $tableinfo[$i]['type']==1} <!--单行文本-->
                <input type="text" name="{$tableinfo[$i]['tableinfo']}" value="">
             {elseif $tableinfo[$i]['type']==2}<!--多行文本-->
                <textarea name="{$tableinfo[$i]['tableinfo']}" style="width:300px !important; height:80px"></textarea>
             {elseif $tableinfo[$i]['type']==3}<!--大型文本-->
                <textarea class="editori" name="{$tableinfo[$i]['tableinfo']}" style="width:100%;height:250px;visibility:hidden;"></textarea>
             {elseif $tableinfo[$i]['type']==4}<!--下拉列表(可改造为单选按钮)-->
                <select name="{$tableinfo[$i]['tableinfo']}" >
                   <?php $chooses=explode("\r\n",$tableinfo[$i]['defvalue']); ?>
                   {loop $chooses $vo}
                       <?php $voar=explode(",",$vo);?>
                       <option value="{$voar[0]}">{$voar[1]}</option>
                   {/loop}
                </select>
             {elseif $tableinfo[$i]['type']==5}<!--上传框（用户可以上传文件，不建议使用）-->
                  <input name="{$tableinfo[$i]['tableinfo']}" id="{$tableinfo[$i]['tableinfo']}" type="text"  value="" />
                  <iframe scrolling="no"; frameborder="0" src="{url("extend/file",array('inputName'=>$tableinfo[$i]['tableinfo']))}" style="width:300px; height:30px;"></iframe>
             {elseif $tableinfo[$i]['type']==6}<!--多选按钮-->
                   <?php $chooses=explode("\r\n",$tableinfo[$i]['defvalue']); ?>
                   {loop $chooses $vo}
                       <?php $voar=explode(",",$vo);?>
                       {$voar[1]} <input type="checkbox" name="{$tableinfo[$i]['tableinfo']}[]" value="{$voar[0]}" />
                   {/loop}
             {/if}
             <tr>
      {/for}
		     <tr>
                <td align="right">验证码：</td> 
                <td>
                <input type="text" name="checkcode" id="checkcode" class="intext" size="4">&nbsp;<img src="{url('index/verify')}" border="0"  height="25" width="50" style=" cursor:hand;" alt="如果您无法识别验证码，请点图片更换" onClick="fleshVerify()" id="verifyImg"/>
                </td>
             </tr>
				<td width="150"></td>
				<td align="left"> <input type="submit" value="提交" class="yx-button"></td>
			</tr>
          </table>
	      </form>
          </div>
          
          
          <div class="box">
          <!--可以调用以下代码显示已审核过的用户提交的表单信息-->
             <table class="table">
               <tr>
               {for $i=1;$i<count($tableinfo);$i++}
                  <th> {$tableinfo[$i]['name']} </th>
			    {/for} 
               </tr>
               
               {loop $list $vo}
                 <tr>
                  {for $i=1;$i<count($tableinfo);$i++}
                   <td> {$vo[$tableinfo[$i]['tableinfo']]} </td>
			      {/for} 
                 </tr>
               {/loop}
               
             </table>
            <div class="pagelist yx-u">{$page}</div>
         </div>
</div>

</div> 