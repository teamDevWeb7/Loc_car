<?php
namespace Core\Framework\Middleware;

use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Router\Router;
use Core\toaster\Toaster;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserAuthMiddleware extends AbstractMiddleware{
    use RedirectTrait;

    private ContainerInterface $container;
    private Router $router;

    public function __construct(ContainerInterface $container){
        $this->container=$container;
        $this->router=$container->get(Router::class);

    }

    public function process(ServerRequestInterface $request){
        $uri=$request->getUri()->getPath();
        if(str_starts_with($uri, '/user')){
            $auth=$this->container->get(UserAuth::class);
            if(!$auth->isLogged() || !$auth->isUser()){
                // si pas co ou si pas user
                $toaster=$this->container->get(Toaster::class);
                $toaster->makeToast("Veuillez vous co pour continuer", Toaster::ERROR);
                return $this->redirect('user.login');
            }
        }
        return parent::process($request);
    }
}