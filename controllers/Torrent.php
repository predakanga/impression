<?php

/*
 * Copyright (c) 2011, predakanga
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Impression\Controllers;

use Fossil\Requests\BaseRequest,
    Impression\Models\Torrent as TorrentModel,
    Impression\Models\TorrentGroup,
    Impression\BDecode,
    Fossil\Plugins\Users\Models\User,
    Impression\Forms\Upload as UploadForm,
    Doctrine\Common\Util\Debug;

/**
 * Description of Upload
 *
 * @author predakanga
 */
class Torrent extends LoginRequiredController {
    /**
     * @F:Inject("Tracker")
     * @var Impression\Trackers\BaseTracker
     */
    protected $tracker;
    
    public function runList() {
        Debug::dump(TorrentModel::findAll($this->container));
    }
    
    public function runUpload(UploadForm $uploadForm) {
        require_once("File/Bittorrent2/Encode.php");
        
        if($uploadForm->isSubmitted() && $uploadForm->isValid()) {
            $decoder = new BDecode;
            $encoder = new \File_Bittorrent2_Encode;
            
            // Create the torrent
            $torrent = TorrentModel::create($this->container);
            $torrent->filename = $uploadForm->file['name'];
            // Process the uploaded file
            $decoder->decodeFile($uploadForm->file['tmp_name']);
            unlink($uploadForm->file['tmp_name']);
            // Sanitise the data
            $sanitisedDict = $this->sanitiseTorrent($decoder->getData());
            $decoder->setData($sanitisedDict);
            $torrent->group = $this->decideTorrentGroup($uploadForm->file['name'], $sanitisedDict);
            // And store it
            $torrent->torrentData = $encoder->encode($sanitisedDict);
            $torrent->infohash = $decoder->getInfoHash(true);
            $torrent->trackerID = 2;
            $torrent->uploadedAt = new \DateTime();
            $torrent->uploader = User::me($this->container);
            $torrent->save();
            return $this->redirectResponse("?controller=torrent&action=list");
        } else {
            return $this->templateResponse("fossil:torrent/upload");
        }
    }
    
    protected function sanitiseTorrent($data) {
        $restricted = array('announce-list',
                            'nodes',
                            'azureus_properties',
                            'libtorrent_resume');
        foreach($restricted as $key) {
            if(isset($data[$key]))
                unset($data[$key]);
        }

        $data['info']['private'] = 1;
        $data['announce'] = "0xDEADBEEF";
        
        return $data;
    }
    
    protected function decideTorrentGroup($filename, $torrentData) {
        $group = TorrentGroup::findOneByName($this->container, $filename);
        
        if(!$group) {
            $group = TorrentGroup::create($this->container);
            $group->name = $filename;
            $group->save();
        }
        
        return $group;
    }
    
    public function runDownload(TorrentModel $id = null) {
        $torrent = $id;

        if(!$torrent) {
            return $this->redirectResponse("?controller=torrent&action=list");
        }
        
        header("Content-Disposition: attachment; filename=\"{$torrent->filename}\"");
        // Replace the announce URL
        $announceURL = $this->tracker->getAnnounceURL(User::me($this->container));
        $announceStr = strlen($announceURL) . ":" . $announceURL;
        
        $toPrint = str_replace("10:0xDEADBEEF", $announceStr, $torrent->torrentData);
        echo $toPrint;
        die();
    }
}

?>
