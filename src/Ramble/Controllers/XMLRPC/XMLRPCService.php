<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 01/08/2017
 * Time: 16:26
 */

namespace Ramble\Controllers\XMLRPC;


use PhpXmlRpc\Encoder;
use PhpXmlRpc\Request;
use Psr\Container\ContainerInterface;
use Ramble\Controllers\AuthorisationInterface;
use Ramble\Models\QueryBuilder;
use Slim\Interfaces\RouterInterface;

abstract class XMLRPCService {
    /**
     * @var bool
     */
    public $debug;

    /**
     * @var array
     */
    protected $methodDefinitions;

    /**
     * @var Encoder
     */
    protected $encoder;

    /**
     * @var AuthorisationInterface
     */
    protected $authInterface;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    public function __construct(ContainerInterface $ci) {
        $this->debug = $ci['ramble']['debug'] ?? false;
        $this->encoder = new Encoder();
        $this->authInterface = $ci['auth']['handler'];
        $this->methodDefinitions = $this->getServiceDefinitions();
        $this->queryBuilder = $ci['queryBuilder'];
        $this->router = $ci['router'];
        $this->logger = $ci['logger'];
    }

    /**
     * @return array
     * The first parameter is the RETURN of the function!
     *
     * Additionally, some clients (charm) will try to be good citizens and send an Int where there should be a string
     * (perhaps the opposite too - though that probably makes you a dick client - looking at you Blogilo!),
     * so those fields use Value::$xmlrpcValue, rather than Value::$xmlrpcString or Value::$xmlrpcInt.
     * PHP can take it - because it's not a hero.
     */
    public abstract function getServiceDefinitions() : array;

    protected function passwordAdmin($user, $pass) {
        return $this->authInterface->checkAuthentication($user, $pass);
    }

    protected function returnValue(Request $req, $value = array(), int $responseCode = 200){
        return new \PhpXmlRpc\Response($this->encoder->encode($value), $responseCode);
    }

    protected function notFound(Request $req){
        return $this->returnValue($req, false, 404);
    }

    protected function badAuth(Request $req){
        return $this->returnValue($req, false, 403);
    }

    protected function unsupportedRequest(Request $req) {
        return $this->returnValue($req, false, 501);
    }
}