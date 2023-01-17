<?php
if(isset($_GET['page'])){
    $page=$_GET['page'];
    switch($page){
        case'addVehicule':
            include dirname(__DIR__)."/view/addVehicule.php";
            break;

        case 'home':
            break;
    }

}else{
    // inclure page d'accueil
}


?>