<?php

namespace AlexNzr\BitUmcIntegration\Core;

use AlexNzr\BitUmcIntegration\Config\Variables;
use AlexNzr\BitUmcIntegration\Utils\Utils;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Router;

class Application implements HttpKernelInterface
{
    private Request         $request;
    private RequestContext  $context;
    private RouteCollection $routes;

    public function __construct()
    {
        $this->setRequest();
        $this->setRequestContext();
        $this->setRouter();
    }

    public function handle(Request $request = null, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        if ($request === null){
            $request = $this->request;
        }

        $matcher = new UrlMatcher($this->routes, $this->context);
        try {
            $request->attributes->add($matcher->match($request->getPathInfo()));
            $controller = $this->getController();
            $postData  = $this->getPostData();
            $response = call_user_func_array($controller, [$postData]);
        } catch (Exception | ResourceNotFoundException $e) {
            $response = new JsonResponse(
                [ "error" => "App error: ".$e->getMessage() ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $response;
    }

    private function setRequest(): void
    {
        $this->request = Request::createFromGlobals();
    }

    private function setRequestContext(): void
    {
        $context = new RequestContext();
        $this->context = $context->fromRequest($this->request);
    }

    private function setRouter(): void
    {
        $this->routes = $this->getRouteCollection();
    }

    private function getRouteCollection(): RouteCollection
    {
        $fileLocator = new FileLocator([dirname(Variables::ROUTES_CONFIG_FILE)]);
        $router = new Router(
            new YamlFileLoader($fileLocator),
            Variables::ROUTES_CONFIG_FILE
        );
        return $router->getRouteCollection();
    }

    private function getController(){
        return (new ControllerResolver())->getController($this->request);
    }

    private function getPostData(): array
    {
        $postData = json_decode($this->request->getContent(),true);
        $postParams = is_array($postData) ? $postData : [];
        return Utils::cleanRequestData($postParams);
    }
}