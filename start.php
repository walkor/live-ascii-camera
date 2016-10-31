<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \Workerman\WebServer;

// 自动加载类
require_once __DIR__ . '/vendor/autoload.php';


$first_frame = '';
$current_frame = '';
$last_byte = '';
$ws_worker = new Worker('Websocket://0.0.0.0:8124');
$ws_worker->onWorkerStart = function($ws_worker)
{
    $connect_worker = new Worker('Websocket://0.0.0.0:8125');
    $connect_worker->listen();
    $connect_worker->onMessage = function(){};
    $ws_worker->ConnectWorker = $connect_worker;
};
$ws_worker->onMessage = function($connection, $data) use ($ws_worker)
{
   foreach($ws_worker->ConnectWorker->connections as $client_connection)
   {
        $client_connection->send($data);
   } 
};

// WebServer
$web = new WebServer("http://0.0.0.0:8123");
// WebServer数量
$web->count = 2;
// 设置站点根目录
$web->addRoot('www.your_domain.com', __DIR__.'/Web');


// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
