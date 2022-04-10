<?php
namespace AlexNzr\BitUmcIntegration\Config;

class Variables
{
    const DEMO_MODE = "Y";
    const PATH_TO_DEMO_DATA_FILE = __DIR__."/../../store/demoData.json";

    const AUTH_LOGIN_1C = 'siteIntegration';
    const AUTH_PASSWORD_1C = '123456';

    const ONE_C_CONNECT_DATA = [
        "PROTOCOL"                  => "http",
        "BASE_ADDR"                 => "localhost:3500",
        "BASE_NAME"                 => "umc_corp",
        "HTTP_SERVICE_PREFIX"       => "hs",
        "HTTP_SERVICE_NAME"         => "siteIntegration",
        "HTTP_SERVICE_API_VERSION"  => "V1",
    ];

    const SCHEDULE_PERIOD_IN_DAYS = 30;
    const DEFAULT_APPOINTMENT_DURATION = 1800;

    const REQUIRED_ORDER_PARAMS = [
        'refUid',
        'surname',
        'name',
        'middleName',
        'orderDate',
        'timeBegin',
        'timeEnd',
        'phone',
        'clinicUid'
    ];

    const PATH_TO_LOG_FILE = __DIR__."/../../log.txt";
    const ROUTES_CONFIG_FILE = __DIR__.'/../../config/routes.yaml';

    const SEP = '/';
    const D_SEP = '//';
    const COLON = ':';
}