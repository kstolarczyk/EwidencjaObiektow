<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ObiektApiControllerTest extends WebTestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testIndex()
    {
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("grupa_obiektow_api");
        $data = [
            'credentials' => [
                'base64_login' => base64_encode("TestUser"),
                'base64_password' => base64_encode("TestPass321")
            ]
        ];
        $client->request("GET", $url, [], [], [], json_encode($data));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider obiektFailData
     */
    public function testDodaj($id, $data)
    {
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_dodaj_api");;
        $client->request("POST", $url, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @dataProvider obiektSuccessData
     */
    public function testDodaj2($id, $data)
    {
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_dodaj_api");;
        $client->request("POST", $url, [], [], [], '{"data":{"obiektId":6,"symbol":"TEST","nazwa":"TestowyObiekt","grupaObiektowId":1,"latitude":15.0,"longitude":15.0,"status":1,"zdjecie":"","ostatniaAktualizacja":"2020-10-02T15:27:13.11329","usuniety":false,"parametry":[{"parametrId":78,"obiektId":6,"typParametrowId":1,"wartosc":"5"},{"parametrId":79,"obiektId":6,"typParametrowId":2,"wartosc":"5"}],"zdjecieLokal":"","hasErrors":false},"credentials":{"base64_login":"a2FtaWxpbmhvMjA=","base64_password":"'.base64_encode("ImKox123").'"}}');
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @dataProvider obiektSuccessData
     */
    public function testEdytuj($id, $data)
    {
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_edytuj_api", ["id" => $id]);
        $client->request("POST", $url, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
    }


    public function testEdytuj2()
    {
        $data = [
            'credentials' => [
                'base64_login' => base64_encode("test"),
                'base64_password' => base64_encode("test")
            ],
            "grupa" => 5,
            "parametry" => [
                ['typ' => 1, 'value' => 155]
            ],
            "symbol" => "TEST XD"
        ];
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_edytuj_api", ["id" => 38]);
        $client->request("POST", $url, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
    }


    public function testUsun()
    {
        $data = '{"credentials":{"base64_login":"a2FtaWxpbmhvMjA=","base64_password":""}}';
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_usun_api", ["id" => 9]);
        $client->request("POST", $url, [], [], [], $data);
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function obiektSuccessData()
    {
        return [
            [40, [
                'credentials' => [
                    'base64_login' => base64_encode("kamilinho20"),
                    'base64_password' => base64_encode("ImKox123")
                ],
                'data' => [
                    'nazwa' => "Chujowy",
                    'symbol' => "Test",
                    'grupa' => 1,
                    'parametry' => [
                        ['typ' => 1, 'value' => 24],
                        ['typ' => 3, 'value' => 45],
                    ],
                    'dlugosc' => 33.327562,
                    'szerokosc' => 41.473215
                ]
            ]],
            [39, [
                'credentials' => [
                    'base64_login' => base64_encode("test"),
                    'base64_password' => base64_encode("test")
                ],
                'nazwa' => "Testowy",
                'symbol' => "Test",
                'grupa' => 1,
                'parametry' => [
                    ['typ' => 1, 'value' => 24],
                    ['typ' => 3, 'value' => 48],
                ],
                'dlugosc' => 23.327562,
                'szerokosc' => 51.473215
            ]],
            [36, [
                'credentials' => [
                    'base64_login' => base64_encode("test"),
                    'base64_password' => base64_encode("test")
                ],
                'nazwa' => "Testowy",
                'symbol' => "Test",
                'grupa' => 1,
                'parametry' => [
                    ['typ' => 1, 'value' => 24],
                    ['typ' => 3, 'value' => 48],
                ],
                'dlugosc' => 23.327562,
                'szerokosc' => 51.473215
            ]],
            [42, [
                'credentials' => [
                    'base64_login' => base64_encode("test"),
                    'base64_password' => base64_encode("test")
                ],
                'nazwa' => "Testowy",
                'symbol' => "Test",
                'grupa' => 1,
                'parametry' => [
                    ['typ' => 1, 'value' => 18],
                    ['typ' => 3, 'value' => 35]
                ],
                'dlugosc' => 25.327562,
                'szerokosc' => 48.473215
            ]]
        ];
    }

    public function obiektFailData()
    {
        return [
            [40, [
                'credentials' => [
                    'base64_login' => base64_encode("test"),
                    'base64_password' => base64_encode("test")
                ],
                'nazwa' => "Testowy",
                'parametry' => [
                    ['typ' => 4, 'value' => 24],
                    ['typ' => 3, 'value' => 45]
                ],
                'dlugosc' => 23.327562,
                'szerokosc' => 51.473215
            ]],
            [35, [
                'credentials' => [
                    'base64_login' => base64_encode("test"),
                    'base64_password' => base64_encode("test")
                ],
                'nazwa' => "Testowy",
                'symbol' => "Test",
                'grupa' => 1,
                'parametry' => [
                    ['typ' => 9, 'value' => 24],
                ],
                'dlugosc' => 23.327562,
                'szerokosc' => 51.473215
            ]],
            [39, [
                'credentials' => [
                    'base64_login' => base64_encode("test"),
                    'base64_password' => base64_encode("test")
                ],
                'nazwa' => "Testowy",
                'symbol' => "Test",
                'grupa' => 1,
                'parametry' => [
                    ['typ' => 1, 'value' => 24],
                    ['typ' => 6, 'value' => 45]
                ],
                'dlugosc' => 23.327562,
                'szerokosc' => 51.473215
            ]]
        ];
    }

}