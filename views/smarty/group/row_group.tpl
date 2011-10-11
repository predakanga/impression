Group #{$index}<br />
ID: {$item->id}<br />
Name: {$item->name}<br />
Torrent count: {$item->torrents|count}<br />
{foreach $item->torrents as $torrent}
    Torrent: #{$torrent@iteration}<br />
    Name: {$torrent->filename}<br />
    Uploaded by: {$torrent->uploader->name}<br />
    Download {link controller="torrent" action="download" id=$torrent->id}here{/link}<br /><br />
{/foreach}<br />
<br />