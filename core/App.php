<?php
namespace Core;
// objet general pour la gestion du site ->manager des objets

// sur composer on a recup guzzlehttp/psr7
// copie le lien, colle dans terminal

use Core\Framework\Renderer\PHPRenderer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Core\Framework\Router\Router;
use Exception;

class App{

    private Router $router;
    // liste modules instanciés
    private array $modules;

    public function __construct(array $modules= [], array $dependencies=[])
    {
        // charger modules et instancier
        $this->router= new Router();
        $dependencies['renderer']->addGlobale('router', $this->router);

        foreach($modules as $module){
            $this->modules[]= new $module($this->router, $dependencies['renderer']);
        }

    }

        public function run(ServerRequestInterface $request) : ResponseInterface{
                // recup url sans le localhost
            $uri=$request->getUri()->getPath();
                // url->string->tableau
                // dernier index c'est 0-1
            if(!empty($uri) && $uri[-1]==='/' && $uri != '/'){
                // si uri pas vide et que dernier carac=/ et que uri entiere pas = juste /
                // renvoie pas reponse pour pas etre dependant de guzzle mais une repsonse qui implemente la response interface
                // withStatus pour redirection, withheader avec soustraction du /
                return (new Response())->withStatus(301)->withHeader('Location', substr($uri, 0, -1));
            }

            $route = $this->router->match($request);
            // verifier !null
            if(is_null($route)){
                return new Response(404, [], "<h2>Cette page n'existe pas</h2>");
            }

            $response = call_user_func_array($route->getCallback(), [$request]);

            if($response instanceof ResponseInterface){
                return $response;
            }elseif(is_string($response)){
                return new Response(200, [], $response);
            }else{
                throw new Exception("Réponse du serveur invalide");
            }


            // $renderer=new PHPRenderer();
            // $path='../view';
            // $renderer->addPath($path);
            // $response=$renderer->render('test', ['name'=>'Cedric']);

            // $path="../App/Home/view";
            // si y a pas le tableau ds render c'es ce qui a ds addGlobale qui s'affiche
            // $renderer->addGlobale('siteName', 'mon site global');
            // $renderer->addPath('Home',$path);
            // $response=$renderer->render('@Home/index', ['siteName'=>'Mon site']);
            
        }
}
// namespace=fichiers virtuels pour aider le programme à trouver endroit

?>