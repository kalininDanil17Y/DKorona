<?php

require_once 'src/Abstract/AbstractHttp.php';
require_once 'src/Abstract/AbstractMigration.php';
require_once 'src/Abstract/AbstractModel.php';
require_once 'src/Abstract/AbstractException.php';

foreach (glob('../src/Controllers/*.php') as $file) {
    require_once $file;
}

require_once 'src/HttpRequest/HttpServer.php';
require_once 'src/HttpRequest/HttpGetParams.php';
require_once 'src/HttpRequest/HttpPostParams.php';

require_once 'src/HttpResponse/HttpResponseBody.php';
require_once 'src/HttpResponse/HttpResponseJson.php';

foreach (glob('../src/Exceptions/*.php') as $file) {
    require_once $file;
}

require_once 'src/Logger.php';
require_once 'src/SystemVar.php';
require_once 'src/View.php';

require_once 'src/HttpRequest.php';
require_once 'src/HttpResponse.php';
require_once 'src/DB.php';

foreach (glob('../src/Models/*.php') as $file) {
    require_once $file;
}

require_once 'src/DKCore.php';
require_once 'src/Router.php';
