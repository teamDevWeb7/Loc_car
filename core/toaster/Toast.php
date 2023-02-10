<?php
namespace Core\toaster;

class Toast{
    public function success(string $message):string{
        return "<div>$message</div>";
    }


    public function error(string $message):string{
        return "<div>$message</div>";
    }


    public function warning(string $message):string{
        return "<div>$message</div>";
    }
}






?>