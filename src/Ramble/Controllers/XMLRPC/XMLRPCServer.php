<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 22/08/16
 * Time: 21:59
 */

namespace Ramble\Controllers\XMLRPC;

use Interop\Container\ContainerInterface;
use PhpXmlRpc\Encoder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramble\Controllers\AuthorisationInterface;
use Ramble\Controllers\Controller;
use Ramble\Controllers\XMLRPC\Blogger;
use Ramble\Controllers\XMLRPC\Metaweblog;
use Ramble\Controllers\XMLRPC\MovingType;
use Ramble\Controllers\XMLRPC\Wordpress;
use Ramble\Controllers\XMLRPC\Service;

class XMLRPCServer extends Controller {
	private $DEBUG = false;
	/**
	 * @var Encoder
	 */
	private $encoder = null;

    /**
     * @var AuthorisationInterface
     */
	private $authorisation = null;

	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);
		$this->DEBUG = $ci["ramble"]["debug"] ?? false;
		$this->encoder = new Encoder();
		$this->authorisation = $ci["auth"]["handler"];
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
		// Easy connection marking
		header('X-XMLRPC: blog XMLRPC');
		// Nice example of how to actually fake an XMLRPC request for testing purposes.
		//ddd($this->getCategories(new \PhpXmlRpc\Request("metaWeblog.editPost", [new \PhpXmlRpc\Value("1", "string"), new \PhpXmlRpc\Value("SacredSkull", "string"), new \PhpXmlRpc\Value("<insert password here>", "string")])));

        // Slim will just ob_start() the output (capture it) for this- Phpxmlrpc doesn't implement the Response/Request
        // interface - so returning it is pointless.
		$this->serve();
	}

	public function serviceDefinitions() : array {
        $arr = [];

        /**
         * @var $services Service[]
         */
        $services = [
            new Blogger($this->ci),
            new Metaweblog($this->ci),
            new MovingType($this->ci),
            new Wordpress($this->ci)
        ];

        foreach($services as $service) {
            foreach ($service->getServiceDefinitions() as $function) {
                $arr = array_merge($arr, $function->toFunctionDescriptor());
            }
        }

        return $arr;
    }

	public function serve(){
		$server = new \PhpXmlRpc\Server($this->serviceDefinitions(), false);

		$server->setDebug($this->DEBUG? 3 : 0);
		$server->exception_handling = 2;

		$server->service();
	}

}
