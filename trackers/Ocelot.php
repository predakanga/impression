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

namespace Impression\Trackers;

use Impression\Models\Torrent,
    Impression\Models\ImpressionUser,
    Fossil\Util\HTTP;

/**
 * Description of Ocelot
 *
 * @author predakanga
 * @F:Object(type="Tracker", name="Ocelot")
 */
class Ocelot extends BaseTracker {
    protected $pdo;
    
    static function usable() { return extension_loaded('pdo') && in_array("mysql", \PDO::getAvailableDrivers()); }
    static function getName() { return "Ocelot"; }
    static function getVersion() { return 1.0; }
    static function getForm() { return null; }
    
    /**
     * @return \PDO
     */
    protected function getPDO() {
        if(!$this->pdo) {
            $db = $this->config['db_dbname'];
            $user = $this->config['db_user'];
            $pass = $this->config['db_pass'];
            if(isset($this->config['db_socket'])) {
                $use_socket = true;
                $socket = $this->config['db_socket'];
            } else {
                $use_socket = false;
                $host = $this->config['db_host'];
                $port = $this->config['db_port'];
            }
            
            if($use_socket) {
                $dsn = "mysql:dbname={$db};unix_socket={$socket}";
            } else {
                $dsn = "mysql:dbname={$db};host={$host};port={$port}";
            }
            $this->pdo = new \PDO($dsn, $user, $pass, array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        }
        return $this->pdo;
    }
    
    protected function runOcelotCommand($action, $params) {
        $host = $this->config['frontend_host'];
        $port = $this->config['frontend_port'];
        $updateKey = $this->config['frontend_pass'];
        
        $uri = "http://{$host}:{$port}/{$updateKey}/update?action={$action}";
        foreach($params as $key => $val) {
            $uri .= "&{$key}={$val}";
        }
        return file_get_contents($uri);
    }
    
    protected function encodeInfohash($hash) {
        return rawurlencode($hash);
    }
    
    public function registerTorrent(Torrent $torrent) {
        // First, insert the torrent into the DB
        $db = $this->getPDO();
        $q = $db->prepare("INSERT INTO torrents (info_hash, Snatched, free_torrent) VALUES (:infohash, 0, 0)");
        $q->bindValue("infohash", $torrent->infohash, \PDO::PARAM_LOB);
        $q->execute();
        $trackerID = $db->lastInsertId();
        
        // Set it to the database
        $torrent->trackerID = $trackerID;
        // Then pass the new torrent over to Ocelot
        $this->runOcelotCommand("add_torrent", array('id' => $trackerID,
                                                     'info_hash' => $this->encodeInfohash($torrent->infohash),
                                                     'freetorrent' => '0'));
    }
    public function removeTorrent(Torrent $torrent) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("DELETE FROM torrents WHERE ID = :trackerid");
        $q->bindValue("trackerid", $torrent->trackerID);
        $q->execute();
        
        // And then update the tracker state
        $this->runOcelotCommand('delete_torrent', array('info_hash' => $this->encodeInfohash($torrent->infohash)));
    }
    
    public function registerUser(ImpressionUser $user) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("INSERT INTO users_main (torrent_pass, can_leech, enabled) VALUES (:torrentpass, 1, \"1\")");
        
        $q->bindValue("torrentpass", $user->passkey);
        $q->execute();

        $user->trackerID = $db->lastInsertId();
        
        // Then update the tracker state
        $this->runOcelotCommand('add_user', array('id' => $user->trackerID, 'passkey' => $user->passkey));
    }
    public function updateUserPasskey(ImpressionUser $user, $oldPasskey) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("UPDATE users_main SET torrent_pass = :torrentpass WHERE ID = :id");
        $q->bindValue("torrentpass", $user->passkey);
        $q->bindValue("id", $user->trackerID);
        $q->execute();
        
        // Then update the tracker state
        $this->runOcelotCommand('change_passkey', array('oldpasskey' => $oldPasskey,
                                                        'newpasskey' => $user->passkey));
    }
    public function updateUserAccess(ImpressionUser $user, $can_leech) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("UPDATE users_main SET can_leech = :can_leech WHERE ID = :id");
        $q->bindValue("id", $user->trackerID);
        $q->bindValue("can_leech", $can_leech ? 1 : 0, \PDO::PARAM_BOOL);
        $q->execute();
        
        // Then update the tracker state
        $this->runOcelotCommand('update_user', array('passkey' => $user->passkey,
                                                     'can_leech' => $can_leech ? "1" : "0"));
    }
    public function removeUser(ImpressionUser $user) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("DELETE FROM users_main WHERE ID = :id");
        $q->bindValue("id", $user->trackerID);
        $q->execute();
        
        // Then update the tracker state
        $this->runOcelotCommand('remove_user', array('passkey' => $user->passkey));
    }
    
    public function getWhitelist() {
        $db = $this->getPDO();
        $q = $db->prepare("SELECT * FROM xbt_client_whitelist");
        $q->execute();
        return $q->fetchAll();
    }
    public function addToWhitelist($clientID) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("DELETE FROM client_whitelist WHERE peer_id = :clientid");
        $q->bindValue("clientid", $clientID);
        $q->execute();
        
        // Update tracker state
        $this->runOcelotCommand('add_whitelist', array('peer_id' => $clientID));
    }
    public function renameOnWhitelist($oldClientID, $newClientID) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("UPDATE xbt_client_whitelist SET peer_id = :newid WHERE peer_id = :oldid");
        $q->bindValue("newid", $newClientID);
        $q->bindValue("oldid", $oldClientID);
        $q->execute();
        
        // Update tracker state
        $this->runOcelotCommand('edit_whitelist', array('old_peer_id' => $oldClientID,
                                                        'new_peer_id' => $newClientID));
    }
    public function removeFromWhitelist($clientID) {
        // Update the DB
        $db = $this->getPDO();
        $q = $db->prepare("DELETE FROM xbt_client_whitelist WHERE peer_id = :clientid");
        $q->bindValue("clientid", $clientID);
        $q->execute();
        
        // Update tracker state
        $this->runOcelotCommand('remove_whitelist', array('peer_id' => $clientID));
    }
    
    public function updateTorrentStates() {
        
    }
    
    public function getAnnounceURL(ImpressionUser $user) {
        $host = $this->config['frontend_host'];
        $port = $this->config['frontend_port'];
        $passkey = $user->passkey;
        
        return "http://{$host}:{$port}/{$passkey}/announce";
    }
}

?>
