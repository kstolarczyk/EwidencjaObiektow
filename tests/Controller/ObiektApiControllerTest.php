<?php


namespace App\Tests\Controller;


use App\Entity\GrupaObiektow;
use App\Repository\ObiektRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ObiektApiControllerTest extends WebTestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->credentials = [
            'base64_login' => base64_encode("TestSuperAdmin"),
            'base64_password' => base64_encode("testsuperadmin1")
        ];
    }

    public array $credentials;

    public function testShouldReturnData() {
        $client = static::createClient();
        $router = self::$container->get("router");
        $grupa = 1;
        $url = $router->generate("obiekt_lista_api", ['id' => $grupa]);
        $client->request("POST", $url, [], [], [], json_encode([
            'credentials' => $this->credentials
        ]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $obiekty = json_decode($client->getResponse()->getContent(), true)['data'];
        $this->assertTrue(count($obiekty) > 0);
    }

    public function testShouldAddEditRemoveObiektTest() {
        $data = [
            'credentials' => $this->credentials,
            'data' => [
                'nazwa' => "Testowy",
                'symbol' => "Test",
                'grupaObiektowId' => 1,
                'parametry' => [
                    ['typParametrowId' => 1, 'wartosc' => 24],
                    ['typParametrowId' => 3, 'wartosc' => "PROJEKT"],
                ],
                'dlugosc' => 23.327562,
                'szerokosc' => 51.473215
            ]
        ];
        $data2 = [
            'credentials' => $this->credentials,
            'data' => [
                'dlugosc' => -23.25, 'parametry' => [
                    ['typParametrowId' => 1, 'wartosc' => 48]
                ]
            ]
        ];
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_dodaj_api");
        $client->request("POST", $url, [], [], [], json_encode($data));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $obiekt = json_decode($client->getResponse()->getContent(), true)['data'][0] ?? null;
        $this->assertNotNull($obiekt);
        $url2 = $router->generate("obiekt_edytuj_api", ['id' => $obiekt['obiektId']]);
        $client->request("POST", $url2, [],[],[], json_encode($data2));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $url3 = $router->generate("obiekt_usun_api", ['id' => $obiekt['obiektId']]);
        $client->request("POST", $url3, [],[],[], json_encode([
            'credentials' => $this->credentials
        ]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function ShouldNotAddObiekt() {
        $data = [
            'credentials' => $this->credentials,
            'data' => [
                'nazwa' => "Testowy",
                'symbol' => "Test",
                'grupaObiektowId' => 1,
                'parametry' => [
//                    ['typParametrowId' => 1, 'wartosc' => 24],
                    ['typParametrowId' => 3, 'wartosc' => "PROJEKT"],
                ],
                'dlugosc' => 23.327562,
                'szerokosc' => 51.473215
            ]
        ];
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_dodaj_api");
        $client->request("POST", $url, [], [], [], json_encode($data));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testShouldNotEditObiekt() {
        $data = [
            'credentials' => $this->credentials,
            'data' => [
                'dlugosc' => -23.25, 'parametry' => [
                    ['typParametrowId' => 1, 'wartosc' => 48]
                ]
            ]
        ];
        $client = static::createClient();
        $router = self::$container->get("router");
        $url = $router->generate("obiekt_edytuj_api", ['id' => 13]);
        $client->request("POST", $url, [], [], [], json_encode($data));
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}