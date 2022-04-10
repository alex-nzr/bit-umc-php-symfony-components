<?php


namespace AlexNzr\BitUmcIntegration\Controller;

use AlexNzr\BitUmcIntegration\Service\MailerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/umc-api")
 */
class MailerController
{
    private MailerService $mailer;

    public function __construct(){
        $this->mailer = new MailerService();
    }

    /**
     * @Route("/email/send"), name="email.send", methods={"POST"})
     */
    public function sendEmail(array $params): Response
    {
        $res = $this->mailer->sendEmail($params);
        return new JsonResponse($res);
    }
}