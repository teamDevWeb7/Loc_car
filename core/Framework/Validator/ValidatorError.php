<?php
namespace Core\Framework\Validator;

class ValidatorError{

    private string $key;
    private string $rule;

    private array $message=[
        'required'=>'Le champs %s est requis'
    ];

    public function __construct(string $key, string $rule){
        $this->key=$key;
        $this->rule=$rule;
    }

    /**
     * transforme l'objet en chaine de caractère pour pouvoir afficher
     *
     * @return string
     */
    public function toString():string{
        if(isset($this->message[$this->rule])){
            // sprintf function affichage attend format string et ce qui doit être inséré =>%s
            return sprintf($this->message[$this->rule], $this->key);
        }
        return $this->rule;
    }
}










?>