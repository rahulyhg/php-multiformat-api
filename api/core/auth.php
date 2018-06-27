<?php

class Auth {
    protected $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function authorizeRequest($userData){
        return isset($userData['token'], $userData['request'], $userData['method']) ? $this->canDoRequest($userData) : false;
    }

    protected function canDoRequest($userData){
        $user = $this->getFromToken($userData['token']);
        if(count($user) > 0){
            $permission = $userData['method']."_".$userData['request'];
            return in_array($permission, json_decode($user[0]['permissions']));
        }
    }

    protected function getFromToken($token){
        return $this->db->select("SELECT u.* FROM users u INNER JOIN user_tokens t ON u.id=t.userId WHERE t.token=:token", [
            "token" => $token
        ]);
    }

    protected function createToken($userId){
        $hashedId = md5($userID);
        $created = md5(microtime());
        return "${hashedId}.${created}";
    }
}