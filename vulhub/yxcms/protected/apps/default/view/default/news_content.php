<?php if(!defined('APP_NAME')) exit;?>

<div class="sidebar inner">
  <div class="sb_nav">
    <h3 class='title myCorner' data-corner='top 5px'>最热信息</h3>
    <div class="active" id="sidebar" data-csnow="5" data-class3="0" data-jsok="2">
	{hot:{table=(news) field=(id,title,color,addtime,method) order=(hits desc,id desc) where=(ispass='1') limit=(5)}}
      <dl class="list-none navnow">
        <dt id='part2_4'><a href='{url($hot['method'],array('id'=>$hot['id']))}'  title='[hot:title]' class="zm"><span>[hot:title $len=13]</span></a></dt>
      </dl>{/hot}
     
      <div class="clear"></div>
    </div>
    <h3 class='title line myCorner' data-corner='top 5px'>联系方式</h3>
    <div class="active editor">
      {piece:lianxi}
      <div class="clear"></div>
    </div>
  </div>
  <div class="sb_box">
    <h3 class="title">
      <div class="position">当前位置： {$daohang}</div>
      <span> 文章正文</span> </h3>
    <div class="clear"></div>
    <div class="active" id="shownews">
      <h1 class="title">{$info['title']}</h1>
	   
      <div class="editor">
        {$info['content']['content']}
        <div class="clear"></div>
      </div>
      <div class="met_hits">
        <div class='metjiathis'>
          <div class="jiathis_style"></div>
          
        </div>
        点击次数：<span>
        
        </span>&nbsp;&nbsp;更新时间：{date($info['addtime'],Y-m-d H:m:i)}&nbsp;&nbsp;【<a href="javascript:window.print()">打印此页</a>】&nbsp;&nbsp;【<a href="javascript:self.close()">关闭</a>】</div>
      <div class="met_page">上一条：{if !empty($upnews)}<a href="{url($upnews['method'],array('id'=>$upnews['id']))}" onFocus="this.blur()">{$upnews['title']}</a>{else}没有了....{/if}&nbsp;&nbsp;下一条：{if !empty($downnews)}<a href="{url($downnews['method'],array('id'=>$downnews['id']))}" onFocus="this.blur()">{$downnews['title']}</a>{else}没有了....{/if}</div>
    </div>
  </div>
  <div class="clear"></div>
</div>
