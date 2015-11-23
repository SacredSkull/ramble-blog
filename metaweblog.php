<?php
require_once "./vendor/autoload.php";
require_once './generated-conf/config.php';

if ($_SERVER['HTTP_HOST'] !== "sacredskull.net") {
    define('DEBUG_SLIM', true);
    define('DEBUG', true);
} else {
    define('DEBUG_SLIM', false);
    define('DEBUG', false);
}

// Easy connection marking
header('X-XMLRPC: blog XMLRPC');

//TODO: if distributing, I'd recommend you change this!
function passwordAdmin($user, $pass){
    return (strcmp($user, "SacredSkull") == 0) && password_verify($pass, '***REMOVED***');
}

use PhpXmlRpc\Value;

function getBlog($req){
    $encoder = new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $user = $params[1];
    $pass = $params[2];

    // param[0] Appkey is irrelevant for now
    $return = array(array(
        "blogid" => 1,
        "url" => "http://" . $_SERVER['SERVER_NAME'],
        "blogName" => "Main",
        "isAdmin" => passwordAdmin($user, $pass),
        "xmlrpc" => "http://" . $_SERVER['SERVER_NAME'] . "/metaweblog.php"
    ));

    if($return[0]["isAdmin"] != 1){
        header('HTTP/1.0 403 Forbidden');
        return new PhpXmlRpc\Response($encoder->encode(array(array())));
    }
    return new PhpXmlRpc\Response($encoder->encode($return));
}

function newPost($req){
    $encoder = new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $blogID = $params[0];
    $username = $params[1];
    $password = $params[2];
    $postStruct = $params[3];
    $publish = $params[4];

    if(!passwordAdmin($username, $password)){
        header('HTTP/1.0 403 Forbidden');
        return new PhpXmlRpc\Response(new PhpXmlRpc\Value("", "string"));
    }
    $post = new Article();
    $post->setDraft(!$publish);
    $cat = explode("|", $postStruct["categories"][0]);
    $post->setCategory(CategoryQuery::create()->findOneByName($cat[0]));
    $post->setTitle($postStruct["title"]);
    $post->setBody($postStruct["description"]);
    $tagArray = explode(',', $postStruct['mt_keywords']);

    foreach ($tagArray as $tag) {
        $exists = TagQuery::create()->filterByName($tag);
        if ($exists->count() < 1) {
            continue;
        } else {
            $exists = $exists->findOne();
        }
        $post->addTag($exists);
    }
    $post->setExcerpt($postStruct["mt_excerpt"]);
    $post->setAllowComments(isset($postStruct["mt_allow_comments"])? $postStruct["mt_allow_comments"] : true);

    $post->save();

    return new PhpXmlRpc\Response(new PhpXmlRpc\Value($post->getId(), "string"));
}

function editPost($req){

    $encoder = new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $postID = $params[0];
    $username = $params[1];
    $password = $params[2];
    $postStruct = $params[3];
    $publish = $params[4];

    if(!passwordAdmin($username, $password)){
        header('HTTP/1.0 403 Forbidden');
        return new PhpXmlRpc\Response(new PhpXmlRpc\Value("false", "boolean"));
    }

    $post = ArticleQuery::create()->findOneById($postID);
    $post->setDraft(!$publish);

    $post->setCategory(CategoryQuery::create()->findOneByName($postStruct["categories"]));
    $post->setTitle($postStruct["title"]);
    $post->setBody($postStruct["description"]);
    $tagArray = explode(',', $postStruct['mt_keywords']);

    foreach ($tagArray as $tag) {
        $exists = TagQuery::create()->filterByName($tag);
        if ($exists->count() < 1) {
            continue;
        } else {
            $exists = $exists->findOne();
        }
        $post->addTag($exists);
    }
    $post->setExcerpt($postStruct["mt_excerpt"]);
    $post->setAllowComments(isset($postStruct["mt_allow_comments"])? $postStruct["mt_allow_comments"] : true);

    $post->save();

    return new PhpXmlRpc\Response(new PhpXmlRpc\Value("true", "boolean"));
}

function getPost($req){
    $encoder = new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $postID = $params[0];
    $username = $params[1];
    $password = $params[2];

    $post = ArticleQuery::create()->findPK($postID);

    $tags = $post->getTags();
    $tagList = "";
    foreach ($tags as $tag) {
        $tagList .= $tag->getName() . ",";
    }

    $postArr = array(
        "postid" => $post->getId(),
        "title" => $post->getTitle(),
        "description" => $post->getBody(),
        "link" => sprintf("%s%s/id/%s", (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"]))? "https://" : "http://", $_SERVER["HTTP_HOST"], $post->getId()),
        "dateCreated" => new PhpXmlRpc\Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
        "date_created_gmt" => new PhpXmlRpc\Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
        "date_modified" => new PhpXmlRpc\Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
        "date_modified_gmt" => new PhpXmlRpc\Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
        "wp_post_thumbnail" => (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"])? "https://" : "http://") . "s3-eu-west-1.amazonaws.com/sacredskull-blog/images/" . $post->getImage(),
        "categories" => array($post->getCategory()->getName()),
        "mt_keywords" => $tagList,
        "mt_excerpt" => "Excerpt does not exist in database schema currently - neither does toggling comments",
        "mt_allow_comments" => "true",
        "wp_slug" => $post->getSlug(),
        "post_draft" => $post->getDraft(),
    );

    return new PhpXmlRpc\Response($encoder->encode($postArr));
}

function getRecentPosts($req){
    $encoder= new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $postCount = $params[3];

    if(strlen($postCount) < 1)
        $postCount = 10;

    $posts = ArticleQuery::create()->lastCreatedFirst()->limit($postCount)->find();

    $allPosts = array(
    );

    foreach($posts as $post){
        $tags = $post->getTags();
        $tagList = "";
        foreach ($tags as $tag) {
            $tagList .= $tag->getName() . ",";
        }

        $postArr = array(
            "postid" => $post->getId(),
            "title" => $post->getTitle(),
            "description" => $post->getBody(),
            "link" => sprintf("%s%s/id/%s", (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"]))? "https://" : "http://", $_SERVER["HTTP_HOST"], $post->getId()),
            "dateCreated" => new PhpXmlRpc\Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
            "date_created_gmt" => new PhpXmlRpc\Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
            "date_modified" => new PhpXmlRpc\Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
            "date_modified_gmt" => new PhpXmlRpc\Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
            "wp_post_thumbnail" => (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"]))?  "https://" : "http://" . "s3-eu-west-1.amazonaws.com/sacredskull-blog/images/" . $post->getImage(),
            "categories" => array($post->getCategory()->getName()),
            "mt_keywords" => $tagList,
            "mt_excerpt" => $post->getExcerpt(),
            "mt_allow_comments" => "true",
            "wp_slug" => $post->getSlug(),
            "post_draft" => $post->getDraft(),
        );

        $allPosts[] = $postArr;
    }

    return new PhpXmlRpc\Response($encoder->encode($allPosts));
}

function getCategories($req){
    $encoder= new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $user = $params[1];
    $pass = $params[2];

    $categoriesArr = "";

    if(!passwordAdmin($user, $pass)){
        header('HTTP/1.0 403 Forbidden');
        return new PhpXmlRpc\Response($encoder->encode($categoriesArr));
    }

    $categories = CategoryQuery::create()->find();

    foreach($categories as $category){
        $categoriesArr[] = array(
            "categoryId" => $category->getId(),
            "categoryName" => $category->getName() . "|" . $category->getColour() . "|" . $category->getFont() . "|" . $category->getAdditionalCSS()
        );
    }

    return new PhpXmlRpc\Response($encoder->encode($categoriesArr));
}

// returns: bool - parameters appKey, postID, username, password, (ignored) publish
function deletePost($req){
    $encoder= new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $postID = $params[1];
    $user = $params[2];
    $pass = $params[3];

    $post = ArticleQuery::create()->findOneById($postID);

    if(!passwordAdmin($user, $pass)){
        header('HTTP/1.0 403 Forbidden');
        return new PhpXmlRpc\Response($encoder->encode($categoriesArr));
    }

    $post->delete();

    return new PhpXmlRpc\Response(new PhpXmlRpc\Value("true", "boolean"));
}

function addCategory($req){
    $encoder= new PhpXmlRpc\Encoder();
    $params = $encoder->decode($req);

    $user = $params[1];
    $pass = $params[2];
    $categoryStruct = $params[3];

    $categoriesArr = array();

    if(!passwordAdmin($user, $pass)){
        header('HTTP/1.0 403 Forbidden');
        return new PhpXmlRpc\Response(new PhpXmlRpc\Value("0", "int"));
    }

    $parsedCatName = explode("|", $categoryStruct["name"]);
    $category = CategoryQuery::create()->findOneByName($parsedCatName[0]);

    if($category->count() == 0){
        $category = new Category();
        $category->setName($parsedCatName[0]);
    }
    if(strlen($parsedCatName[1]) > 0)
        $category->setColour($parsedCatName[1]);
    $category->setRoot($parsedCatName[0]);
    if(strlen($parsedCatName[2]) > 0)
        $category->setFont($parsedCatName[2]);
    if(strlen($parsedCatName[3]) > 0)
        $category->setAdditionalCSS($parsedCatName[3]);

    $category->save();

    return new PhpXmlRpc\Response(new PhpXmlRpc\Value($category->getId(), "int"));
}

$srv = new PhpXmlRpc\Server(array(
    // returns: int (category ID) - parameters blogID, username, password, category struct
    "wp.newCategory" => array(
        "function" => "addCategory",
        "signature" => array(array(Value::$xmlrpcInt, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct)),
        "docstring" => "Creates a category. Parameters: blogID, username, password, category struct/array name"
    ),
    // returns: array - parameters blogID, username, password
    "metaWeblog.getCategories" => array(
        "function" => "getCategories",
        "signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString)),
        "docstring" => "Returns categories. Parameters: blogID, username, password"
    ),
    // returns: bool - parameters appKey, postID, username, password, (ignored) publish
    "blogger.deletePost" => array(
        "function" => "deletePost",
        "signature" => array(array(Value::$xmlrpcBoolean, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcBoolean)),
        "docstring" => "Deletes a post, returns true if deleted. Parameters: appKey, postID, username, password, publish bool (ignored)",
    ),
    // returns: array of struct - parameters: API key, username, password
    "blogger.getUsersBlogs" => array(
        "function" => "getBlog",
        "signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString)),
        "docstring" => 'Gets blog info',
    ),
    // returns: string - parameters: $blogid, $username, $password, $struct, $publish
    "metaWeblog.newPost" => array(
        "function" => "newPost",
        "signature" => array(array(Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct, Value::$xmlrpcBoolean)),
        "docstring" => 'Creates a new post from the associative array (parameter 4)',
    ),
    // returns: bool - parameters: $postid, $username, $password, $struct, $publish
    "metaWeblog.editPost" => array(
        "function" => "editPost",
        "signature" => array(array(Value::$xmlrpcBoolean, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct, Value::$xmlrpcBoolean)),
        "docstring" => 'Edits the post of a a certain ID (first parameter)',
    ),
    // returns: struct - parameters: $postid, $username, $password
    "metaWeblog.getPost" => array(
        "function" => "getPost",
        "signature" => array(array(Value::$xmlrpcStruct, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString)),
        "docstring" => 'Gets a specific post.',
    ),
    // returns: array - blogID, username, password, noOfPosts
    "metaWeblog.getRecentPosts" => array(
        "function" => "getRecentPosts",
        "signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcInt)),
        "docstring" => 'Gets a certain number of recent posts (final parameter)',
    ),
));

// require_once dirname(__FILE__) . '/xmlrpc.php';

// function metaWeblog_newPost($params) {
//   list($blogid, $username, $password, $struct, $publish) = $params;
//   $title = $struct['title'];
//   $description = $struct['description'];


//   // YOUR CODE:
//   $post_id = 0; // id of the post you just created


//   XMLRPC_response(XMLRPC_prepare((string)$post_id), WEBLOG_XMLRPC_USERAGENT);
// }

// function metaWeblog_editPost($params) {
//   list($postid, $username, $password, $struct, $publish) = $params;


//   // YOUR CODE:
//   $result = false; // whether or not the action succeeded


//   XMLRPC_response(XMLRPC_prepare((boolean)$result), WEBLOG_XMLRPC_USERAGENT);
// }

// function metaWeblog_getPost($params) {
//   list($postid, $username, $password) = $params;
//   $post = array();


//   // YOUR CODE:
//   $post['userId'] = '1';
//   $post['dateCreated'] = XMLRPC_convert_timestamp_to_iso8601(time());
//   $post['title'] = 'Replace me';
//   $post['content'] = 'Replace me, too';
//   $post['postid'] = '1';


//   XMLRPC_response(XMLRPC_prepare($post), WEBLOG_XMLRPC_USERAGENT);
// }

// function XMLRPC_method_not_found($methodName) {
//   XMLRPC_error("2", "The method you requested, '$methodName', was not found.", WEBLOG_XMLRPC_USERAGENT);
// }

// $xmlrpc_methods = array(
//     'metaWeblog.newPost'  => 'metaWeblog_newPost',
//     'metaWeblog.editPost' => 'metaWeblog_editPost',
//     'metaWeblog.getPost'  => 'metaWeblog_getPost',
//     // 'blogger.newPost'  => 'metaWeblog_newPost',
//     // 'blogger.editPost' => 'metaWeblog_editPost',
//     // 'blogger.getPost'  => 'metaWeblog_getPost'
//     );

// $xmlrpc_request = XMLRPC_parse($HTTP_RAW_POST_DATA);
// $methodName = XMLRPC_getMethodName($xmlrpc_request);
// $params = XMLRPC_getParams($xmlrpc_request);

// if(!isset($xmlrpc_methods[$methodName])) {
//   XMLRPC_method_not_found($methodName);
// } else {
//   $xmlrpc_methods[$methodName]($params);
// }
