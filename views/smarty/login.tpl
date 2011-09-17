{extends file="fossil:loggedOut"}
{block name=assignations}
{assign var="title" value="Login" scope=global}{/block}
{block name=content}

{if $error}
    {$error}
{/if}
{form name="Login"}

<div class="box-6">
    <div class="box">
{link action="signup"}Sign up{/link}
    </div>
</div>

{/block}