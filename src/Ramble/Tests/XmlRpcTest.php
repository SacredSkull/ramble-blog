<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 00:52
 */
namespace {
    require __DIR__ . '/../vendor/autoload.php';
}

namespace Ramble\Tests {

    use Mockery;
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
    use Monolog\Logger;
    use Negotiation\Exception\InvalidArgument;
    use PhpXmlRpc\Request;
    use PhpXmlRpc\Response;
    use PhpXmlRpc\Server;
    use PhpXmlRpc\Value;
    use Ramble\Controllers\AuthorisationInterface;
    use Ramble\Controllers\XMLRPC;
    use Ramble\Models\Article;
    use Ramble\Models\ArticleQuery;
    use Ramble\Models\Category;
    use Ramble\Models\CategoryQuery;
    use Ramble\Models\QueryBuilder;
    use Slim\Container;
    use Slim\Router;
    use PHPUnit\Framework\TestCase;

    abstract class XmlRpcTest extends TestCase
    {
        use MockeryPHPUnitIntegration;

        public $methodNamespace = '';
        /**
         * @var Container
         */
        protected $ci = null;
        /**
         * @var Server
         */
        protected $xmlrpcServer = null;
        /**
         * @var XMLRPC
         */
        protected $xmlrpcController = null;

        /**
         * @var QueryBuilder|Mockery\Mock
         */
        protected $queryBuilder = null;

        public function setUp() {
            $this->ci = new Container();
            $this->ci['queryBuilder'] = function(){
                return Mockery::mock(QueryBuilder::class);
            };
            $this->ci['logger'] = function() {
                return \Mockery::mock(Logger::class)
                    ->shouldIgnoreMissing();
            };
            $this->ci['router'] = function() {
                return \Mockery::mock(Router::class)
                    ->shouldIgnoreMissing();
            };
            $_SERVER['SERVER_NAME'] = 'google.com/';

            $this->ci['auth'] = function() {
                return ['handler' => function() {return true;}];
            };
            $this->queryBuilder = $this->ci['queryBuilder'];
        }

        public function tearDown() {
            \Mockery::close();
        }

        public function getUsersBlogs(bool $validAuth, $method = 'getUsersBlogs') : Response{
            $this->authMock($validAuth);

            $this->ci->router->shouldReceive('pathFor')->andReturn('xmlrpc');

            $request = $this->createRequest($method, array(
                new Value(rand(0, 200000)),
                new Value("blah", Value::$xmlrpcString),
                new Value("asd", Value::$xmlrpcString)
            ));

            return $this->xmlrpcServer->service($request->serialize('UTF-8'), false);
        }

        public function deletePost(bool $validAuth, $method = 'deletePost') {
            $postID = rand(1, 250);

            $articleMock = Mockery::mock(Article::class)
                ->shouldIgnoreMissing();

            if($validAuth) {
                $articleMock
                    ->shouldReceive('delete')
                    ->once()
                    ->withNoArgs();
            } else {
                $articleMock
                    ->shouldNotReceive('delete')
                    ->withAnyArgs();
            }

            $articleQueryMock = Mockery::mock(ArticleQuery::class)
                ->shouldReceive('findOneById')
                ->with($postID)
                ->andReturn($articleMock)
                ->getMock();

            $this->queryBuilder
                ->shouldReceive('ArticleQuery')
                ->andReturn($articleQueryMock);
            $this->authMock($validAuth);

            $request = $this->createRequest($method, array(
                new Value(rand(0, 200000)),
                new Value($postID),
                new Value("blah", Value::$xmlrpcString),
                new Value("asd", Value::$xmlrpcString),
                new Value(true, Value::$xmlrpcBoolean)
            ));

            return $this->xmlrpcServer->service($request->serialize('UTF-8'), false);
        }

        public function GetCategories(bool $validAuth, $methodName = 'getCategories') {
            $fakeCat1 = new Category();
            $fakeCat2 = new Category();

            $fakeCat1
                ->setId(1)
                ->setName('First')
                ->setSlug('first');
            $fakeCat2
                ->setId(2)
                ->setName('Second')
                ->setSlug('second');

            $this->ci->router->shouldReceive('pathFor')->andReturnValues([
                'http://blog.io/category/first',
                'http://blog.io/category/first'
            ]);

            $categoryQueryMock = Mockery::mock(CategoryQuery::class)
                ->shouldReceive('find')
                ->withNoArgs()
                ->andReturn([$fakeCat1, $fakeCat2])
                ->getMock();

            $this->queryBuilder
                ->shouldReceive('CategoryQuery')
                ->andReturn($categoryQueryMock);
            $this->authMock($validAuth);

            $request = $this->createRequest($methodName, array(
                new Value(rand(0, 200000)),
                new Value("blah", Value::$xmlrpcString),
                new Value("asd", Value::$xmlrpcString)
            ));

            return $this->xmlrpcServer->service($request->serialize('UTF-8'), false);
        }

        /**
         * @param \ArrayAccess|array $list
         * @param \ArrayAccess|array $haystack
         */

        /*
         * ASSERTIONS
         */

        public function assertArrayContainsKeys($list, $haystack) {
            foreach ($list as $key)
                self::assertArrayHasKey($key, $haystack);
        }

        public function assertGoodAuth(Response $response) {
            self::assertTrue($response->faultCode() === 0,
                "Got an error in a valid case: {$response->faultString()}");
        }

        public function assertBadAuth(Response $response){
            self::assertTrue($response->faultCode() === 403,
                "Bad login details did not fail: {$response->faultString()}");
            self::assertTrue($response->faultCode() !== 1,
                "A miscellaneous error occurred instead: {$response->faultString()}");
            self::assertTrue($response->value() === 0,
                "Instead of returning nothing, was given '{$response->value()}'");
        }

        public function authMock(bool $validAuth){
            $authorisationInterface = Mockery::mock(AuthorisationInterface::class);
            $authorisationInterface->shouldReceive('checkAuthentication')->andReturn($validAuth);

            $this->ci['auth'] = function() use ($authorisationInterface) {
                return ['handler' => $authorisationInterface];
            };

            $this->xmlrpcController = new XMLRPC($this->ci);
            $this->xmlrpcServer = new Server($this->xmlrpcController->serviceDefinitions(), false);
        }

        public function createRequest($methodName, array $params, $namespace = null) : Request {
            $namespace = $namespace ?? $this->methodNamespace;
            if(strlen($this->methodNamespace) > 0) {
                $methodName = $this->methodNamespace . '.' . $methodName;
            } else {
                $methodName = 'system.' . $methodName;
            }

            return new Request($methodName, new Value($params, Value::$xmlrpcArray));
        }
    }
}