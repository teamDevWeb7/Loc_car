<?php
namespace Core\Session;

use Core\Session\SessionInterface;

// session n'a rien à voir avec une co ex: si j'ai un panier en tant qu'incvité tout va quand même dans la session

class PHPSession implements SessionInterface{

    public function __construct(){
        $this->startIfNot();
    }

    public function set(string $key, $value):void{
        // ecrase qd multi
        $this->startIfNot();
        $_SESSION[$key]=$value;

    }

    public function setArray(string $key, $value):void{
        $this->startIfNot();
        // si index $key existe pas il est créé et ensuite existe en tant que tab, donc qd on rajoute new value elle ne crase plus mais push
        $_SESSION[$key][]=$value;
    }

    public function get(string $key, $default=null){
        $this->startIfNot();
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key):bool{
        $this->startIfNot();
        return isset($_SESSION[$key]);
    }

    public function delete(string $key): void{
        $this->startIfNot();
        // supprime la clé avec unset
        unset($_SESSION[$key]);
    }



    private function startIfNot(){
        // use de fonctions natives php pr check si y a pas de session qui est deja en route avant d'en ouvrir une
        if(session_status()=== PHP_SESSION_NONE){
            session_start();
        }
    }



}





?>