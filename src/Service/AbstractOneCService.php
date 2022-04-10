<?php
namespace AlexNzr\BitUmcIntegration\Service;

use AlexNzr\BitUmcIntegration\Config\Variables;
use AlexNzr\BitUmcIntegration\Utils\Utils;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


abstract class AbstractOneCService{
    protected string $endpoint;
    protected string $authToken;
    protected HttpClientInterface $client;
    protected $demoData;

    public function __construct()
    {
        $this->client = HttpClient::create();

        $this->endpoint = "";
        foreach (Variables::ONE_C_CONNECT_DATA as $param => $value)
        {
            $separator = $param === "PROTOCOL" ? Variables::COLON . Variables::D_SEP : Variables::SEP;
            $this->endpoint .= $value . $separator;
        }

        $this->authToken = base64_encode(Variables::AUTH_LOGIN_1C.Variables::COLON.Variables::AUTH_PASSWORD_1C);

        if (Variables::DEMO_MODE === "Y"){
            try {
                $this->demoData = json_decode(file_get_contents(Variables::PATH_TO_DEMO_DATA_FILE), true);
            }catch (Exception $e){}
        }
    }

    /** send request to 1C database
     * @param array $params
     * @return string
     */
    public function post(array $params = []): string
    {
        try {
            $response = $this->client->request('POST', $this->endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $this->authToken,
                    'Content-Type' => 'application/json;charset=utf-8',
                ],
                'body' => json_encode($params),
            ]);

            return $response->getContent();
        }
        catch (Exception | TransportExceptionInterface  | ClientExceptionInterface
        | RedirectionExceptionInterface | ServerExceptionInterface $e )
        {
            return Utils::addError($e->getMessage());
        }
    }
}