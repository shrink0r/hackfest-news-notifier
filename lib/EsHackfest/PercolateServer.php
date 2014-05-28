<?php

namespace EsHackfest;

use React\ZMQ\Context;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use React\EventLoop\Factory as EventLoopFactory;

class PercolateServer
{
    protected $poll_socket_options;

    protected $push_socket_options;

    public function __construct(array $poll_socket_options, array $push_socket_options)
    {
        $this->poll_socket_options = $poll_socket_options;
        $this->push_socket_options = $push_socket_options;
    }

    public function run()
    {
        $event_loop = EventLoopFactory::create();
        $event_pusher = new EventPusher();

        $context = new Context($event_loop);
        $pull_socket = $context->getSocket(ZMQ::SOCKET_PULL);

        $pull_socket->bind(
            sprintf(
                'tcp://%s:%s',
                $this->poll_socket_options['host'],
                $this->poll_socket_options['port']
            )
        );

        // sent by command handlers via zmq
        $pull_socket->on('message', array($event_pusher, 'onNewEvent'));

        $web_socket = new Server($event_loop);
        $web_socket->listen(''
            $this->push_socket_options['port'],
            $this->push_socket_options['host']
        );

        $web_server = new IoServer(
            new HttpServer(
                new WsServer($event_pusher)
            ),
            $web_socket
        );

        $event_loop->run();
    }
}
