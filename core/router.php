<?php
// recup url de la page
if(isset($_GET['page'])){
    $page=$_GET['page'];
    switch($page){
        case'addVehicule':
            // on va sur controler car c'est lui qui gere ce qu'il  y a dans la page, ce que veut le user
            include dirname(__DIR__)."/controler/addVehiculeControler.php";
            break;

        case 'home':
            break;
    }

}else{
    // inclure page d'accueil
}


?>