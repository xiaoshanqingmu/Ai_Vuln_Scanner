<?php if(!defined('APP_NAME')) exit;?>
<link href="__PUBLIC__/css/highslide.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/highslide.js"></script>
<script language="javascript">
hs.graphicsDir = '__PUBLIC__/images/graphics/';
hs.wrapperClassName = 'wide-border';
hs.showCredits = false;
</script>
<div class="sidebar inner">
  <div class="sb_nav">
    <h3 class='title myCorner' data-corner='top 5px'>产品展示</h3>
    <div class="active" id="sidebar" data-csnow="8" data-class3="0" data-jsok="2">
 {loop $sorts $key $vo}  
      {if strpos($vo['path'],'100024')!==false && $key!='100024'}              
      <dl class="list-none navnow">
        <dt id='part2_7'><a href='{$vo['url']}'  title='{$vo['name']}' class="zm"><span>{$vo['name']}</span></a></dt>
      </dl>{/if}
{/loop}
     
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
      <div class="position">当前位置 {$daohang}</div>
      <span>产品介绍</span> </h3>
    <div class="clear"></div>
    <div class="active" id="showproduct">
      <h1 class='title'>{$info['title']}</h1>
      <dl class='pshow'>
        <dt><span class='info_img' id='imgqwe'><img src={$PhotoImgPath}{$info['picture']}re']} alt='示例产品七' title='示例产品七' width=350 height=350  /></span>
        </dt>
        <dd>
          <ul class="list-none">
            <li><span>型号</span>23199-78901
              <div style="clear:both;"></div>
            </li>
            <li><span>品牌</span>全友
              <div style="clear:both;"></div>
            </li>
            <li><span>价格</span>
              100
              <div style="clear:both;"></div>
            </li>
			<li><span>描述</span>
            <div style="width:320px; height:auto;">{$info['content']}</div>
              <div style="clear:both;"></div>
            </li>
          </ul>
        </dd>
      </dl>
      <div style="clear:both;"></div>
      <h3 class="ctitle"><span>其他样式</span>
        <div class="clear"></div>
      </h3>
      <div class="editor">
	  {loop $photolist $vo}
                <A onClick="return hs.expand(this)" title="{$vo['tit']}" href="{$PhotoImgPath}{$vo['picture']}"><img width="135" height="100" alt="{$vo['tit']}" class="box" src="{$PhotoImgPath}thumb_{$vo['picture']}"></A>
                {/loop} 
        <div></div>
        <div class="clear"></div>
      </div>
      <div class="met_hits">
        <div class='metjiathis'>
          <div class="jiathis_style"></div>
          <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1346378669840136" charset="utf-8"></script>
        </div>
        点击次数：<span>
        <script language='javascript' src='../include/hits.php?type=product&id=3'></script>
        </span>&nbsp;&nbsp;更新时间：{date($info['addtime'],Y-m-d H:m:i)}&nbsp;&nbsp;【<a href="javascript:window.print()">打印此页</a>】&nbsp;&nbsp;【<a href="javascript:self.close()">关闭</a>】</div>
      <div class="met_page">上一条：{if !empty($upnews)}<a href="{url($upnews['method'],array('id'=>$upnews['id']))}" onFocus="this.blur()">{$upnews['title']}</a>{else}没有了....{/if}&nbsp;&nbsp;下一条：{if !empty($downnews)}<a href="{url($downnews['method'],array('id'=>$downnews['id']))}" onFocus="this.blur()">{$downnews['title']}</a>{else}没有了....{/if}</div>
    </div>
  </div>
  <div class="clear"></div>
</div>
