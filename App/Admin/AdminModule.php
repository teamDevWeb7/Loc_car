<?php
namespace App\Admin;

use App\Admin\Action\AdminAction;
use App\Admin\Action\AuthAction;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\RendererInterface;
use core\Framework\AbstractClass\AbstractModule;

class AdminModule extends AbstractModule{

    private Router $router;
    private RendererInterface $renderer;
    // private ContainerInterface $container;
    public const DEFINTIONS =__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(ContainerInterface $container){
        // $container si besoin dans les methodes sinon balec
        // $this->container=$container;
        $this->router=$container->get(Router::class);
        $this->renderer=$container->get(RendererInterface::class);
        $AuthAction=$container->get(AuthAction::class);
        $AdminAction=$container->get(AdminAction::class);

        $this->renderer->addPath('admin', __DIR__.DIRECTORY_SEPARATOR.'view');

        $this->router->get('/admin/login', [$AuthAction, 'login'], 'admin.login');
        $this->router->post('/admin/login', [$AuthAction, 'login']);
        $this->router->get('/admin/home', [$AdminAction, 'home'], 'admin.home');
        $this->router->get('/admin/logout', [$AuthAction, 'logout'], 'admin.logout');
    }
    
}