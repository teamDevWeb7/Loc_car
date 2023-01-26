<?php
namespace App\Car;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class CarModule{
    private Router $router;
    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router=$router;
        $this->renderer=$renderer;

        $this->renderer->addPath('Car', __DIR__.DIRECTORY_SEPARATOR.'view');

        $this->router->get('/addVehicule', [$this, 'addVehicule'], 'addVehicule');

        $this->router->post('/addVehicule', [$this, 'saveCar']);
    }

    public function addVehicule(): string{
        return $this->renderer->render('@Car/addVehicule');
    }

    public function saveCar(ServerRequestInterface $request): string{
        $data=$request->getParsedBody();
        var_dump($data);
        return $this->renderer->render('@Car/addVehicule');
    }


}

?>