<?php
ini_set("display_errors",1);
require 'Slim/Slim.php';
require 'utils.php';
//if($_POST)
//{
//    echo "asdfasdf";
//    exit;
//}

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$context = new ZMQContext();

$socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
$socket->connect("tcp://127.0.0.1:6666");

$app->zmpContext = $context;
$app->zmqSocket = $socket;

include_once 'downloadREST.php';
include_once 'linkREST.php';

// download
$app->get(      '/downloads/status',               'getDownloadStatus');
$app->get(      '/downloads/refresh',              'refreshDownloads');
$app->get(      '/downloads/refresh/:id',          'refreshDownload');
$app->get(      '/downloads',                      'getDownloads');
$app->get(      '/downloads/search/:query',         'findDownloadByName');
$app->get(      '/downloads/availability/:id',         'checkDownloadAvailability');
$app->get(      '/downloads/:id',                   'getDownload');
$app->post(     '/downloads',                      'addDownload');
$app->post(     '/downloads/remove',               'deleteDownloads');
$app->post(     '/downloads/start',                'startDownloads');
$app->post(     '/downloads/stop',                 'stopDownloads');
$app->post(     '/downloads/import',               'importDownloads');
$app->put(      '/downloads/:id',                   'updateDownload');
$app->delete(   '/downloads/:id',                   'deleteDownload');

$app->post(     '/downloads/test',                  'test');
// link
$app->get(      '/links/status',                'getLinkStatus');
$app->get(      '/links/refresh',               'refreshLinks');
$app->get(      '/links/refresh/:id',           'refreshLink');
$app->get(      '/links',                       'getLinks');
$app->get(      '/links/:id',                   'getLink');
$app->get(      '/links/search/:query',         'findLinkByName');
$app->put(      '/links/:id',                   'updateLink');
$app->delete(   '/links/:id',                   'deleteLink');
$app->post(     '/links',                       'addLink');
$app->post(     '/links/remove',                'deleteLinks');
$app->post(     '/links/addDownloadFromLink',   'addDownloadFromLink');
$app->post(     '/links/addDownloadsFromLinks', 'addDownloadsFromLinks');

$app->run();
