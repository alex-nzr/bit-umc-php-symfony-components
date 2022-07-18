## **Info**
**Integration written for a custom 1c extension. It doesn't work with typical web services.**

## Usage

### 1. Fill required options
Go to `src/Config/Variables.php` and fill params to access your 1C base.
```
AUTH_LOGIN_1C
AUTH_PASSWORD_1C
PROTOCOL
BASE_ADDR
BASE_NAME
```

### 2. Create entry point
Create a folder `umc-api` in the root of your project and a file `app.php` in it  the following content:
```
require_once '/path_to_your_composer_json/vendor/autoload.php';

use AlexNzr\BitUmcIntegration\Core\Application;

try
{
    $app = new Application();
    $response = $app->handle();
    $response->send();
}
catch (Exception $e)
{
    echo json_encode(["error" => $e->getMessage()]);
}
```

### 3. Create .htaccess
Create in the `umc-api` folder a file `.htaccess` with the following content:
```
RewriteEngine On
RewriteRule .* app.php [L]
```
This is necessary so that all requests to the application folder are directed to the entry point and processed by the application's router.

### 4. Install 1C extension
Download `siteIntegration.cfe` and insert this extension in your 1c base.
Then publish it on web-server.

### ***Documentation is still under development...***

[API documentation](https://app.swaggerhub.com/apis-docs/alex-nzr/Bit-umc-integration/1.0.0#/)


### Get all clients
Request url
```
/umc-api/client/list
```
Request body(json). Optionally to get one
```
{
    "clientUid": "d4f6fdf5-38a6-11e4-8012-20cf3029e98b"
}
```

Success response data(json)
```
[
    {
        "name": "Аркадий",
        "surname": "Ахмин",
        "middlename": "Николаевич",
        "inn": "1211321231",
        "snils": "030-213132121",
        "birthday": "1967-04-10T00:00:00",
        "displayBirthday": "10-04-1967",
        "gender": "M",
        "uid": "d4f6fdf5-38a6-11e4-8012-20cf3029e98b",
        "isAppointmentBlocked": "N",
        "contacts": {
            "phone": "+71234567890",
            "emailHome": "example_home@gmail.com"
            "emailWork": "example_work@gmail.com"
        },
        "relatives": [
            {
                "uid": "20cf30-38a6-11e4-8012-20cf3029e98b",
                "name": "Ахмин Иван Аркадьевич"
                "relation": "Сын"
            },
            {
                "uid": "9e98b0-38a6-11e4-8012-20cf3029e98b",
                "name": "Иванова Ксения Николаевна"
                "relation": "Сестра"
            },
            {
                //uid can be undefined if relative is not in Catalog.Clients
                "name": "Ахмин Петр Николаевич"
                "relation": "Брат"
            },
        ]
    }
]
```

Error response data(json)
```
{
    error: "something went wrong..."
}
```