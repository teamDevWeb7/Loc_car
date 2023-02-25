<?php
namespace Core\Session;

interface SessionInterface{
    public function get(string $key, $default=null);

    public function set(string $key, $value):void;

    public function setArray(string $key, $value):void;

    // savoir si clé existe
    public function has(string $key):bool;

    public function delete(string $key): void;


}




?>
