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
        $(this).children().css('background-color', '#f4f8ff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	}
	);	
  });
</script>
<title>网站配置</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
		<div class="list_head_ml">当前位置：【网站配置】</div>
		<div class="list_head_mr"></div>
		</div>

		<table width="100%" border="0" cellpadding="0" cellspacing="1"  class="all_cont">
			<form action="{url('set/index')}" method="post" id="info" name="info">
            <tr><th colspan="3" align="center">站点基本信息设置</th></tr>
			<tr>
				<td align="right">站点名称：</td>
				<td><input id="sitename" class="regular-text" type='text' value="{$config['sitename']}" name="sitename" /></td>
				<td width="40%"></td>
			</tr>
			<tr>
				<td align="right">网站地址：</td>
				<td><input class="regular-text" type='text' value="{$config['siteurl']}" name="siteurl" id="siteurl" /></td>
				<td></td>
			</tr>
			<tr>
				<td align="right">网站关键词：</td>
				<td><input class="regular-text" type='text' value="{$config['keywords']}" name="keywords" id="keywords" size="60"/></td>
				<td></td>
			</tr>
			<tr>
				<td align="right">网站描述：</td>
				<td><textarea name="description" id="description" cols="60" rows="5" class="regular-textarea">{$config['description']}</textarea></td>
				<td></td>
			</tr>
            <tr>
				<td align="right">联系电话：</td>
				<td><input class="regular-text" type='text' value="{$config['telephone']}" name="telephone" id="telephone" /></td>
				<td></td>
			</tr>
            <tr>
				<td align="right">QQ：</td>
				<td><input class="regular-text" type='text' value="{$config['QQ']}" name="QQ" id="QQ" /></td>
				<td class="inputhelp">多个QQ号用半角,隔开</td>
			</tr>
			<tr>
				<td align="right">站长邮箱：</td>
				<td><input class="regular-text" type='text' value="{$config['email']}" name="email" id="email" /></td>
				<td></td>
			</tr>
            <tr>
				<td align="right">公司地址：</td>
				<td><input class="regular-text" type='text' value="{$config['address']}" name="address" id="address" /></td>
				<td></td>
			</tr>
			<tr>
				<td align="right">ICP备案号：</td>
				<td><input class="regular-text" type='text' value="{$config['icp']}" name="icp" id="icp" /></td>
				<td></td>
			</tr>
            <tr><th colspan="3">核心设置</th></tr>
			<tr>
				<td align="right">调试模式：</td>
				<td><?php if($config['APP']['DEBUG']){ ?> 
				<input type="radio" name="APP[DEBUG]" value="true" checked="checked" />开启 
				<input type="radio" name="APP[DEBUG]" value="false" />关闭 
				<?php }else{?> 
				<input type="radio" name="APP[DEBUG]" value="true" />开启 
				<input type="radio" name="APP[DEBUG]" value="false" checked="checked" />关闭 
				<?php }?></td>
				<td class="inputhelp">开启调试模式将会显示详细错误信息</td>
			</tr>
           <tr>
          <td align="right">数据库永久链接：</td>
          <td><?php if($config['DB']['DB_PCONNECT']){ ?>
            <input type="radio" name="DB[DB_PCONNECT]" value="true"   checked="checked"/>开启
            <input type="radio" name="DB[DB_PCONNECT]" value="false" />关闭
            <?php }else{?>
            <input type="radio" name="DB[DB_PCONNECT]" value="true" />开启
            <input type="radio" name="DB[DB_PCONNECT]"  value="false"  checked="checked" />关闭
            <?php }?></td>
           <td class="inputhelp">开启后减少数据库链接获取和释放操作，但是将长期占用数据库链接资源</td>
        </tr>
        <tr>
          <td align="right">数据库缓存：</td>
          <td><?php if($config['DB']['DB_CACHE_ON']){ ?>
            <input type="radio" name="DB[DB_CACHE_ON]"  value="true"   checked="checked"/>开启
            <input type="radio" name="DB[DB_CACHE_ON]" value="false" />关闭
            <?php }else{?>
            <input type="radio" name="DB[DB_CACHE_ON]" value="true" />开启
            <input type="radio" name="DB[DB_CACHE_ON]"  value="false"  checked="checked" />关闭
            <?php }?></td>
          <td class="inputhelp">开启后减少数据库操作，但数据库更新在数据库缓存时间内将不能够实时显示。若设置永久缓存，更新后请<a href="{url('set/clear')}">清空数据库缓存</a></td>
        </tr>
        <tr>
          <td align="right">数据库缓存时间：</td>
          <td><input class="regular-text" type='text' value="{$config['DB']['DB_CACHE_TIME']}" name="DB[DB_CACHE_TIME]" size="7" />秒</td>
          <td class="inputhelp">0为不缓存,-1为永久缓存</td>
        </tr>
        <tr>
          <td align="right">模版缓存：</td>
          <td><?php if($config['TPL']['TPL_CACHE_ON']){ ?>
            <input type="radio" name="TPL[TPL_CACHE_ON]"  value="true"   checked="checked"/>开启
            <input type="radio" name="TPL[TPL_CACHE_ON]" value="false" />关闭
            <?php }else{?>
            <input type="radio" name="TPL[TPL_CACHE_ON]" value="true" />开启
            <input type="radio" name="TPL[TPL_CACHE_ON]"  value="false"  checked="checked" />关闭
            <?php }?></td>
          <td class="inputhelp">开启后减少模板标签编译过程，提高PHP运行速度，但是模板更改后需要<a href="{url('set/clear')}">清空模板缓存</a>后才可显示更新</td>
        </tr>
        <tr>
          <td align="right">静态缓存：</td>
          <td><?php if($config['APP']['HTML_CACHE_ON']){ ?>
            <input type="radio" name="APP[HTML_CACHE_ON]"  value="true"   checked="checked"/>开启
            <input type="radio" name="APP[HTML_CACHE_ON]" value="false" />关闭
            <?php }else{?>
            <input type="radio" name="APP[HTML_CACHE_ON]" value="true" />开启
            <input type="radio" name="APP[HTML_CACHE_ON]"  value="false"  checked="checked" />关闭
            <?php }?></td>
          <td class="inputhelp">开启后在静态缓存时间内将直接访问静态页，最大限度提高网站速度。如果设置缓存时间较长不能实时更新内容请<a href="{url('set/clear')}">清空静态缓存</a>(下个版本提供静态详细设置)</td>
        </tr>
        <tr>
          <td align="right">URL规则：</td>
          <td>
             <textarea cols="70" rows="9" name="REWRITE"><?php
				 if(!empty($config['REWRITE'])){
				    foreach ($config['REWRITE'] as $key => $value) {
					  echo $key.'='.$value."\r\n";
					}
				 }
                 ?></textarea>
          </td>
          <td class="inputhelp">该项可以不必设置内容,制定url重写规则有利于缩短网址、SEO优化和隐藏.php后缀等，如果您不清楚如何设置url规则,可以使用以下规则代码：<br>
            &lt;c&gt;/&lt;a&gt;-&lt;id&gt;-&lt;page&gt;-&lt;keywords&gt;-&lt;type&gt;.html=default/&lt;c&gt;/&lt;a&gt;<br>
            &lt;c&gt;/&lt;a&gt;-&lt;id&gt;-&lt;page&gt;-&lt;keywords&gt;.html=default/&lt;c&gt;/&lt;a&gt;<br>
            &lt;c&gt;/&lt;a&gt;-&lt;id&gt;-&lt;page&gt;.html=default/&lt;c&gt;/&lt;a&gt;<br>
            index.html=default/index/index<br>
            &lt;c&gt;/&lt;a&gt;-&lt;id&gt;.html=default/&lt;c&gt;/&lt;a&gt;<br>
            &lt;c&gt;/&lt;a&gt;.html=default/&lt;c&gt;/&lt;a&gt;<br>
            如果设置规则后导致网站链接无法访问，请检查：<br>
            1、您的规则格式是否正确。<br>
            2、Apache网站根目录下是否正确设置了.htaccess或是IIS下是否正确设置了httpd.ini<br>
          </td>
        </tr>
        <tr><th colspan="3">上传设置</th></tr>
        <tr>
          <td align="right">编辑器上传大小：</td>
          <td><input class="regular-text" type='text' value="{$config['fileupSize']}" name="fileupSize" id="fileupSize" size="7"/>&nbsp;B以内</td>
          <td class="inputhelp">编辑器上传单个文件限制</td>
        </tr>
        <tr>
          <td align="right">图片上传大小：</td>
          <td><input class="regular-text" type='text' value="{$config['imgupSize']}" name="imgupSize" id="imgupSize" size="7"/>&nbsp;B以内</td>
          <td class="inputhelp">全站编辑器外图片单张上传限制</td>
        </tr>
        <tr>
          <td align="right">图片水印：</td>
          <td><?php if($config['ifwatermark']){ ?>
            <input type="radio" name="ifwatermark" id="ifwatermark" value="true"   checked="checked"/>开启
            <input type="radio" name="ifwatermark" value="false" />关闭
            <?php }else{?>
            <input type="radio" name="ifwatermark" value="true" />开启
            <input type="radio" name="ifwatermark" id="ifwatermark" value="false"  checked="checked" />关闭
            <?php }?></td>
          <td class="inputhelp">开启后所有上传图片将会做标记</td>
        </tr>
        <tr>
          <td align="right">水印图片：</td>
          <td>public/watermark/<input class="regular-text" type='text' value="{$config['watermarkImg']}" name="watermarkImg" id="watermarkImg" /></td>
          <td class="inputhelp">图片请选择png格式</td>
        </tr>
        <tr>
          <td align="right">水印位置：</td>
          <td><input class="regular-text" type='text' value="{$config['watermarkPlace']}" name="watermarkPlace" id="watermarkPlace" size="2" maxlength="1"/></td>
          <td class="inputhelp">0为随机位置，1为顶端居左，2为顶端居中，3为顶端居右，4为中部居左，5为中部居中，6为中部居右，7为底端居左，8为底端居中，9为底端居右</td>
        </tr>
        <tr>
          <td align="right">图集缩略图最大宽度：</td>
          <td><input class="regular-text" type='text' value="{$config['thumbMaxwidth']}" name="thumbMaxwidth" id="thumbMaxwidth" size="7"/>&nbsp;px</td>
          <td></td>
        </tr>
        <tr>
          <td align="right">图集缩略图最大高度：</td>
          <td><input class="regular-text" type='text' value="{$config['thumbMaxheight']}" name="thumbMaxheight" id="thumbMaxheight" size="7"/>&nbsp;px</td>
          <td></td>
        </tr>
        <tr>
          <td align="right">文章封面图宽度：</td>
          <td><input class="regular-text" type='text' value="{$config['coverMaxwidth']}" name="coverMaxwidth" id="coverMaxwidth" size="7"/>&nbsp;px</td>
          <td></td>
        </tr>
        <tr>
          <td align="right">文章封面图高度：</td>
          <td><input class="regular-text" type='text' value="{$config['coverMaxheight']}" name="coverMaxheight" id="coverMaxheight" size="7"/>&nbsp;px</td>
          <td></td>
        </tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td align="left" colspan="2"><input type="submit" value="修改" class="button"></td>
			</tr>
			</form>
		</table>

</div>
</body>
</html>