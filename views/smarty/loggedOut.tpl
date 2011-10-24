<html>
  <head>{block name=assignations}{/block}
    <title>Welcome to Impression</title>
    {block name="css"}
    <link type="text/css" rel="stylesheet" href="static/raptor.css" />
    <link href='//fonts.googleapis.com/css?family=Rosario' rel='stylesheet' type='text/css' />
    {/block}
  </head>
  <body class="rbx hasLeftCol">
      <img src="static/gradient.png" alt="background" style="position: fixed; top: 41px; left: 0; width: 100%; height: 100%; z-index: -1;"/>
    <div id="purpleBar"></div>
    <div id="globalContainer">
      <div id="pageHeader" class="headerBar clearfix">
        <h1 id="pageLogo">
          <a title="Home" href="index.html"></a>
        </h1>
        <div id="headNav" class="clearfix">
        </div>
      </div>
      <div id="content" class="rb_content clearfix">
        <div id="mainContainer">
          <div id="leftCol">
          </div>
          <div id="contentCol" class="clearfix" style="background-color: #815A94; padding-bottom: 1px;">
              <div style="margin: -33px 10px 10px 10px; background-color: white;">
                {block name="content_layout"}
                <div id="contentArea">
                  {block name="content"}
                  <div style="float: right; margin-top: 3px;">
                    <h5>Sidebar</h5>
                  </div>
                  <h2 style="font-size: 16px;">Your feed</h2>
                  {/block}
                </div>
                <div id="bottomContent">
                </div>
                {/block}
              </div>
          </div>
        </div>
      </div>
      <div id="pageFooter"></div>
    </div>
  </body>
</html>