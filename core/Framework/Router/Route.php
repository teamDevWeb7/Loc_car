<?php
namespace Core\Framework\Router;


class Route{
    private string $name;
    private $callable;
    private array $params;

    /**
     * enregistre les infos liées à notre route
     *
     * @param string $name nom de la route(ex: user.login)
     * @param [type] $callable fonction de controler a appeler lors du match de la route
     * @param array $params tableau parametre de la route
     */
    public function __construct(string $name, $callable, array $params){
        $this->name=$name;
        $this->callable=$callable;
        $this->params=$params;
    }
    

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of callable
     * retourne la fonction de controler liée à la route
     */ 
    public function getCallback()
    {
        return $this->callable;
    }

    /**
     * Get the value of params
     */ 
    public function getParams()
    {
        return $this->params;
    }
}


?>