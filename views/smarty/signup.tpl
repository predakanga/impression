{extends file="fossil:loggedOut"}
{block name=assignations}
{assign var="title" value="Login" scope=global}{/block}
{block name=content}

{if $error}
    {$error}
{/if}
{form name="Signup"}

{/block}