<?php

use Aws\S3\S3Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

define('DEBUG_SLIM', true);
define('DEBUG', true);
define('WIREFRAME', false);
define('CERT_AUTH', true);
define('SITE_ROOT', realpath(dirname(__FILE__)));
define('USING_PARSEDOWN', false);
if (defined('USING_WINDOWS')) {
    define('USING_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));
}
define('USING_BBCODE', false);

if (DEBUG == true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

// Composer
require './vendor/autoload.php';
// Propel auto-conf
require './generated-conf/config.php';
// Defined BBCodes, if set
if (USING_BBCODE) {
    require './lib/bbcodes.php';
}

// Either use cheap network access control list or expensive
// but quality client certificate authorisation
// Defined via the CERT_AUTH constant.
// Unfortunate that Cloudflare doesn't support two-way AUTH :(
// Instead nginx passes the on or off parameter to PHP based on
// the domain name. Obviously only certain domains have this turned to
// on.
function isAdmin()
{
    if (CERT_AUTH) {
        if (($_SERVER['HTTPS'] == "on" && $_SERVER['VERIFIED'] == "SUCCESS") || $_SERVER['VERIFIED'] == "OVERRIDE") {
            return true;
        }

        return false;
    } else {
        // use a cookie for auth?
        $allowedips = array('127.0.0.1', '192.168.1.102');
        $ips = $_SERVER['REMOTE_ADDR'];
        if (in_array($ips, $allowedips)) {
            return true;
        }

        return false;
    }
}

function jsFriendly($string)
{
    return htmlspecialchars($string, ENT_QUOTES);
}

$GLOBALS['execute_time'] = microtime(true);

$logger = new Logger('defaultLogger');
$logger->pushHandler(new StreamHandler('./logs/propel.log'));
\Propel\Runtime\Propel::getServiceContainer()->setLogger('defaultLogger', $logger);

session_start();

$defaultTheme = new ThemeQuery();
if (!$defaultTheme->findPK(1)) {
    $theme = new Theme();
    $theme->setName('Stuff');
    $theme->setRoot('/');
    $theme->setColour('#66C4F0');
    $theme->save();
}

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => './templates',
    'debug' => DEBUG_SLIM,
));

$view = $app->view();

$view->parserOptions = array(
    'cache' => dirname(__FILE__).'/cache',
    'debug' => DEBUG,
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    //new TwigExtensionParsedown(),
    new \Twig_Extensions_Extension_Text(),
    new \SacredSkull\Blog\TwigExtensionHTMLTruncaterFilter(),
    new \SacredSkull\Blog\TwigExtensionExecutionTime(),
);

$sayings = explode("\n", file_get_contents('include/etc/skull-phrases.txt'));
$random = rand(0, sizeof($sayings)-1);

$quote = $sayings[$random];

$themes = ThemeQuery::create()
    ->setQueryKey('get_all_themes')
    ->find();

$homepageHandler = function ($page = 1) use ($app, $quote, $themes) {
    $maxPerPage = 10;

    // Paginate() is currently not compatible with setQueryKey, and only caches the first
    // count query, which is useless because then it causes Twig to throw an exception
    // because propel threw an exception. It was horrible to diagnose and you'd better take
    // your own word for it!
    //
    // TL;DR - paginate() & setQueryKey() do not play well together currently!
    $posts = ArticleQuery::create()
        //->setQueryKey('homepage')
        ->orderById('DESC')
        ->filterByDraft(false)
        ->paginate($page, $maxPerPage);

    $maxPages = ceil($posts->getNbResults() / $maxPerPage);
    $pagelist = $posts->getLinks(5);

    if ($page == 1 && $app->request()->getPathInfo() != "/") {
        $app->redirect('/');
    }

    if ($page > $maxPages) {
        $app->flash('denied', "I've failed you senpai.. I haven't got that many post pages!");
        $app->redirect('/');
    }

    $app->render('home.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'posts' => $posts,
        'current_page' => $page,
        'page_list' => $pagelist,
        'themes' => $themes,
        'max_pages' => $maxPages,
    ));
};

$testHandler = function () use ($app, $quote, $logger) {

    $generator = Faker\Factory::create('en_UK');

    if (isAdmin()) {
        for ($i = 1; $i < 20; $i++) {
            if ($i == 19) {
                $theme = new Theme();
                $theme->setName('A real category!');
                $theme->setRoot('/');
                $theme->setColour('gold');
                $theme->save();

                $post = new Article();
                $post->setDraft(false);
                $post->setTitle('An article that was actually typed!');
                $post->setBody("#A post's header\n##Some subtext for the header.\n**Finally, a bit of *bold***\n\n{img a:My namesake's avatar t:The not-so household logo of SacredSkull}test.jpg{/img}");
                $post->setTheme($theme);
                $post->save();
                break;
            }
            $theme = new Theme();
            $theme->setName($generator->company);
            $theme->setRoot('/');
            $theme->setColour($generator->hexcolor);
            $theme->save();

            $post = new Article();
            $post->setDraft(false);
            $post->setTitle($generator->realText(25));
            $post->setBody($generator->realText(4000));
            $post->setTheme($theme);
            $post->save();
        }
    }

    $logger->info("All test values inserted correctly!");

    $app->render('test.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        //'posts' => $posts,
        //'json_theme' => $jsonThemes
    ));
};

$postIDHandler = function ($idArticle) use ($app, $quote) {
    $post = ArticleQuery::create()->findPK($idArticle);
    $app->render('post.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'post' => $post,
    ));
};

$postSlugHandler = function ($year, $month, $day, $slugArticle) use ($app, $quote) {
    $slug = $year."-".$month."-".$day."_".$slugArticle;

    $post = ArticleQuery::create()->findOneBySlug($slug);

    if ($post == null) {
        // 404 page...
        $app->notFound();
    }

    $app->render('post.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'post' => $post,
    ));
};

$postsDateHandler = function ($year, $month = 1, $day = 1, $page = 1) use ($app, $quote, $themes) {

    $date = strtotime($year."/".$month."/".$day);

    $maxPerPage = 10;

    // See the main home route ("/") for information about why setQueryKey should always be commented out (for now) for paginations
    $posts = ArticleQuery::create()
        //->setQueryKey('homepage')
        ->orderById('DESC')
        ->filterByCreatedAt($date)
        ->paginate($page, $maxPerPage);

    $maxPages = ceil($posts->getNbResults() / $maxPerPage);
    $pagelist = $posts->getLinks(5);

    if ($page > $maxPages) {
        $app->flash('denied', "I'm afraid that is all I posted on $date :(");
        //$app->redirect('/');
    }

    $app->render('home.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'posts' => $posts,
        'current_page' => $page,
        'page_list' => $pagelist,
        'pagination_url' => '/'.$year."/".$month."/".$day."/",
        'themes' => $themes,
        'max_pages' => $maxPages,
    ));
};

$adminHandler = function ($id = -1) use ($app, $defaultTheme) {
    if (!isAdmin()) {
        $app->notFound();
    }

    // GET request handler
    if ($app->request->isGet()) {
        if ($id == -1) {
            $post = new Article();
            $post->setTitle('');
            $post->setBody('');
            $preparedFromWebFromArray = array('New Post', '#Excited');
            $tagArray = implode(',', $preparedFromWebFromArray);
            $post->setTags($tagArray);
            $post->setTheme($defaultTheme->findPK(1));
            $post->setDraft(true);

            $post->save();

            $app->flash('mode', 'new');
            $app->redirect('/admin/'.$post->getId());

            $quote = "  CREATION MODE!  ";
            $app->render('create.php', array(
                'debug' => DEBUG,
                'wireframe' => WIREFRAME,
                'skull_greeting' => $quote,
                'mode' => 'new',
            ));
        } else {
            $quote = "  EDITING #".$id."!  ";

            $post = ArticleQuery::create()->findPK($id);
            $app->render('create.php', array(
                'debug' => DEBUG,
                'wireframe' => WIREFRAME,
                'skull_greeting' => $quote,
                'mode' => 'edit',
                'post' => $post,
            ));
        }
    } elseif ($app->request->isPost()) {
        echo "ASD!!!";
        $post = ArticleQuery::create()->findOneById($id);
        $allPostVars = $app->request->post();
        $post->setTitle($allPostVars['title']);
        $post->setBody($allPostVars['body']);
        $post->save();
        echo $post->getId();
        if (!$app->request->isAjax()) {
            $app->redirect('/');
        }
    }
};

$app->get('/test', $testHandler);
$app->get('/test/', $testHandler);

// Update post by ID
$app->post('/admin/:id', $homepageHandler)->conditions(array('id' => '\d{1,10}'));

$app->get('/page/:page', $homepageHandler)->conditions(array(
    'page' => '\d{1,4}',
));

$app->get('/id/:idArticle', $postIDHandler)->conditions(array(
    'idArticle' => '\d{1,10}',
));

$app->get('/:year/:month/:day/:slugArticle', $postSlugHandler)->conditions(array(
    'year' => '(19|20)\d\d',
    'month' => '\d\d',
    'day' => '\d\d',
    'slugArticle' => '[a-zA-Z0-9_.-]+',
));

$app->get('/category/:slugTheme', function ($slugTheme) use ($app, $quote) {
    $theme = ThemeQuery::create()->findOneBySlug($slugTheme);
    $themedPosts = ArticleQuery::create()
        /*->setQueryKey('posts_of_particular_theme')*/
        ->filterByTheme($theme);
    var_dump($themedPosts);
});

// Admin page
$app->get('/admin(/:id)', $adminHandler);

// Admin page, edit an existing post.
//$app->get('/admin/:id', $adminHandler)->conditions(array('id' => '\d{1,10}'));

$app->get('/admin/:slugArticle', function ($slugArticle) use ($app) {
    $quote = "  EDITING SLUG ".$slugArticle."!  ";
    $post = ArticleQuery::create()->findOneBySlug($slugArticle);
    $app->render('create.php', array(
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'mode' => 'edit',
    ));
});

$app->group('/api', function () use ($app) {
    $app->get('/post', function () use ($app) {
        $app->redirect('/api/posts/', 301);
    });
    $app->get('/posts/', function () {
        $allPosts = ArticleQuery::create()->find();
        $posts[] = null;
        foreach ($allPosts as $post) {
            $title_js_ready = jsFriendly($post->getTitle());
            $theme_js_ready = jsFriendly($post->getTheme()->getName());
            $posts[$post->getId()] = array('title' => $title_js_ready, 'theme' =>  $theme_js_ready, 'id' => $post->getId() );
        }
        unset($posts[0]);
        echo json_encode((object) $posts);
    });
    $app->get('/post/:id', function ($id) {
        // Output JSON snippet of specific post Id
    })->conditions(array('id' => '\d{1,10}'));

    $app->get('/post/:slugArticle', function ($slugArticle) {
        // Output JSON snippet of specific post slug
    });
});

$app->get('/upload/:post', function ($post) use ($app) {
    isAdmin() ? $app->redirect('/admin/'.$post) : $app->redirect('/');
});

$app->post('/upload/:post', function ($post) use ($app) {
    if (isAdmin()) {
        $local_path = SITE_ROOT."/images/uploads/".$post."/".str_replace(' ', '_', $_FILES['file']['name']);
        $remote_path = "images/".$post."/".str_replace(' ', '_', $_FILES['file']['name']);

        // Trivia: @ operator suppresses error messages!
        @mkdir(SITE_ROOT."/images/uploads/".$post);

        $temp = $_FILES['file']['tmp_name'];
        move_uploaded_file($temp, $local_path);

        if (USING_WINDOWS) {
            // Use ugly, inefficient exec() work-around for PATH issues & general DLL nonsense on Windows.
            exec("E:\\WPNXM\\bin\\imagick\\convert.exe ".$local_path." -resize 600x600 ".$local_path);
        } else {
            // Nice, it's not a Windows environment! We can actually use OOP
            // programming without having to exec() something!
            die("This section hasn't been set up for proper linux operation. Please complete the
                relevant ImageMagick / GraphicsMagick code");
        }

        $client = S3Client::factory(array(
            'key' => 'AKIAJ6LFRWILE2M7YCGA',
            'secret' => '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',
        ));

        $filepath = './include/img/skull.png';
        $result = "";
        try {
            $result = $client->putObject(array(
                'Bucket' => 'sacredskull-blog',
                'Key'          => $remote_path,
                'SourceFile'   => $local_path,
                'ContentType'  => $_FILES['file']['type'],
                'ACL'          => 'public-read',
                'StorageClass' => 'STANDARD',
                'Metadata'     => array(
                    'category' => 'image',
                ),
            ));
            echo '{"url": "'.$remote_path.'"}';
        } catch (S3Exception $e) {
            echo '{"error": "'.$e->getMessage().'"}';
        }
    } else {
        $app->flash('denied', "Sorry, you aren't allowed in ".$req->getResourceUri()."!");
        $app->redirect('/');
    }
})->conditions(array('post' => '\d{1,10}'));

$app->get('/:year(/:month(/:day/(/:page)))', function ($year, $month = 1, $day = 1, $page = 1) use ($app, $quote, $themes) {

    $date = strtotime($year."/".$month."/".$day);
    echo $date;

    $maxPerPage = 10;

    // See the main home route ("/") for information about why setQueryKey should always be commented out for paginations
    $posts = ArticleQuery::create()
        //->setQueryKey('homepage')
        ->orderById('DESC')
        ->filterByCreatedAt($date)
        ->paginate($page, $maxPerPage);

    $maxPages = ceil($posts->getNbResults() / $maxPerPage);
    $pagelist = $posts->getLinks(5);

    if ($page > $maxPages) {
        $app->flash('denied', "I'm afraid that is all I've posted on $date :(");
        //$app->redirect('/');
    }

    $app->render('home.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'posts' => $posts,
        'current_page' => $page,
        'page_list' => $pagelist,
        'pagination_url' => '/'.$year."/".$month."/".$day."/",
        'themes' => $themes,
        'max_pages' => $maxPages,
    ));
})->conditions(array('post' => '\d{1,10}'));

$app->get('/', $homepageHandler);

$app->run();
