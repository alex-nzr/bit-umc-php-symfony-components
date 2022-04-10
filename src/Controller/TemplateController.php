<?php

namespace AlexNzr\BitUmcIntegration\Controller;

use AlexNzr\BitUmcIntegration\Service\OneCReader;
use AlexNzr\BitUmcIntegration\Service\TemplateService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/umc-api")
 */
class TemplateController
{

    private TemplateService $paramsGenerator;

    public function __construct()
    {
        $this->paramsGenerator = new TemplateService();
    }

    /**
     * @Route("/", name="template.main", methods={"GET"})
     */
    public function showTemplateList(): Response
    {
        return $this->render('index.html.twig', [
            //'user_first_name' => $userFirstName,
            //'notifications' => $userNotifications,
        ]);
    }

    /**
     * @Route("/template/popup", name="template.popup", methods={"GET"})
     */
    public function showExamplePopup(): Response
    {
        $widgetParams = $this->paramsGenerator->generateTemplateParams();
        return $this->render('popup/index.html.twig', $widgetParams);
    }

    private function render(string $template, array $params = []): Response
    {

        return new Response($template);
    }
}