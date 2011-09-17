<html>
  <head>{block name=assignations}{/block}
    <title>Impression - {$title}</title>
    <link type="text/css" rel="stylesheet" href="static/raptor.css" />
    <link href='//fonts.googleapis.com/css?family=Rosario' rel='stylesheet' type='text/css' />
  </head>
  <body class="rbx hasLeftCol">
    <div id="purpleBar"></div>
    <div id="globalContainer">
      <div id="pageHeader" class="headerBar clearfix">
        <h1 id="pageLogo">
          <a title="Home" href="index.html"></a>
        </h1>
        <div id="headNav" class="clearfix">
          <div class="lfloat">
            <form id="navSearch">
              <div class="wrap">
                <span class="textInput">
                  <span>
                    <input type="textarea"></input>
                    <button title="Search" type="submit"></button>
                  </span>
                </span>
              </div>
            </form>
          </div>
          <div class="rfloat">
            <ul id="pageNav">
              <li>
                <a href="index.html">Home</a>
              </li>
              <li>
                <a href="profile.html">Profile</a>
              </li>
              <li></li>
              <li>
                <a href="account.html">Account</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div id="content" class="rb_content clearfix">
        <div id="mainContainer">
          <div id="leftCol">
            <a href="profile.html" class="profileImg iconImg">
              <img src="http://media.steampowered.com/steamcommunity/public/images/avatars/4e/4e8dc5c1fa6ae4f081eaec596799ebe9f7268afc_full.jpg" />
            </a>
            <div id="userDetails">
              <a href="profile.html">Predatory Kangaroo</a>
              <a href="profile.html">Power User</a>
              <a href="snatchlist.php?type=down"><img src="static/downArrow.png" /> 0</a>
              <a href="snatchlist.php?type=up"><img src="static/upArrow.png" /> 3</a>
              <a href="snatchlist.php?type=hnr"><img src="static/hnr.png" /> 7</a>
            </div>
            <div id="navBar">
              <ul>
                <li class="selectedItem"><a href="home.html"><span class="imgWrap"><img src="static/news.png" /></span>News Feed</a></li>
                <li><a href="inbox.html"><span class="imgWrap"><img src="static/messages.png" /></span>Messages</a></li>
                <li><hr /></li>
                <li><a href="schedule.html"><span class="imgWrap"><img src="static/schedule.png" /></span>Schedule</a></li>
                <li><a href="shows.html"><span class="imgWrap"><img src="static/videos.png" /></span>TV Shows</a></li>
                <li><hr /></li>
                <li><a href="games.html"><span class="imgWrap"><img src="static/games.png" /></span>Games</a></li>
                <li><a href="forums.html"><span class="imgWrap"><img src="static/forums.png" /></span>Forums</a></li>
              </ul>
            </div>
          </div>
          <div id="contentCol" class="clearfix hasRightCol">
            {block name="content_layout"}
            <div id="rightCol">
              <div class="right_column">
                <div class="comp_box">
                  <div class="comp_header">
                    <h4>Upcoming Shows</h4>
                  </div>
                  <div class="comp_body with_icon">
                    <img src="static/schedule.png" />
                    <div class="minischedule_item">
                      <a href="series.html">The Simpsons</a><br />
                      6x21 - The PTA Disbands<br />
                      16th April at 18:30
                    </div>
                    <div class="minischedule_item">
                      <a href="series.html">Leverage</a><br />
                      3x15 - The Big Bang Job<br />
                      20th December at 10:00
                    </div>
                    <div class="minischedule_item">
                      <a href="series.html">Leverage</a><br />
                      3x16 - The San Lorenzo Job<br />
                      20th December at 11:00
                    </div>
                  </div>
                </div>
                <div class="comp_box clearfix">
                  <div class="comp_header">
                    <h4>Current Poll</h4>
                  </div>
                  <div class="comp_body">
                    <h5>Will we get sued for infringement?</h5>
                    <form>
                      <input type="radio"></input><label>Yes</label><br />
                      <input type="radio"></input><label>No</label><br />
                      <input type="radio"></input><label>For the design, or...?</label><br /><br />
                      <input type="submit"></input>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div id="contentArea">
              {block name="content"}
              <div style="float: right; margin-top: 3px;">
                <h5>Sidebar</h5>
              </div>
              <h2 style="font-size: 16px;">Your feed</h2>
              {/block}
            </div>
            <div id="bottomContent">
              Bottom content
            </div>
            {/block}
          </div>
        </div>
      </div>
      <div id="pageFooter"></div>
    </div>
  </body>
</html>