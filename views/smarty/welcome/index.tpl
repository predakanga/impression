{extends file="fossil:loggedOut"}
{block name=content}
<div style="text-align: center; line-height: 36pt;" class="clearfix">
    <br />
    <img src="static/teapot_impression.png" /><br /><br />
    <span style="font-family: 'Rosario', sans-serif; font-size: 48pt; font-weight: 600;">Welcome to Impression</span>
    <br />
    <br />
    <br />
    <br />
    <span style="width: 50%; font-family: 'Rosario', sans-serif; font-size: 36pt; font-weight: 600;">{link controller="login" action="signup"}Signup{/link}</span><span style="float: left; width: 50%; font-family: 'Rosario', sans-serif; font-size: 36pt; font-weight: 600;">{link controller="login" action="login"}Login{/link}</span>
</div>
{/block}