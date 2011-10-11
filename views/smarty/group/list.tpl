{extends file="fossil:loggedIn"}
{block name=content}
<h2>List of groups</h2>
{paginate source=$groups}
{/block}