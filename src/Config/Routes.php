<?php
namespace AlexNzr\BitUmcIntegration\Config;

use AlexNzr\BitUmcIntegration\Controller\OneCController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * @deprecated
 */
class Routes
{
    /** get collection of routes for application
     * @return RouteCollection
     */
    public static function getRoutes(): RouteCollection
    {
        trigger_error("Deprecated class called.", E_USER_NOTICE);

        $controller = OneCController::class;
        $routes = new RouteCollection();

        $routesData = [
            ["name" => "clinics.list",   "path"   => "/clinics/list",    'controller' => $controller, 'method' => "getClinicsList"],
            ["name" => "clients.list",   "path"   => "/clients/list",    'controller' => $controller, 'method' => "getClientsList"],
            ["name" => "employees.list", "path"   => "/employees/list",  'controller' => $controller, 'method' => "getEmployeesList"],
            ["name" => "schedule.get",   "path"   => "/schedule",        'controller' => $controller, 'method' => "getSchedule"],
            ["name" => "order.create",   "path"   => "/order/create",    'controller' => $controller, 'method' => "createOrder"],
        ];

        foreach ($routesData as $route) {
            $routes->add($route["name"], new Route(
                $route["path"],
                [
                    'controller' => $route["controller"],
                    'method'     => $route["method"],
                ]
            ));
        }

        return $routes;
    }
}