<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Message\Socket
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Message\Socket;

use phpOMS\Socket\Server\ClientManager;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Router\SocketRouter;
use phpOMS\Message\Socket\Request;
use phpOMS\Message\Socket\Response;

/**
 * Server class.
 *
 * Parsing/serializing arrays to and from php file
 *
 * @package phpOMS\Message\Socket
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class PacketManager
{
    /**
     * Socket router.
     *
     * @var   SocketRouter
     * @since 1.0.0
     */
    private SocketRouter $router;

    /**
     * Dispatcher.
     *
     * @var   Dispatcher
     * @since 1.0.0
     */
    private Dispatcher $dispatcher;

    /**
     * Constructor.
     *
     * @param SocketRouter $router     Socket router
     * @param Dispatcher   $dispatcher Dispatcher
     *
     * @since 1.0.0
     */
    public function __construct(SocketRouter $router, Dispatcher $dispatcher)
    {
        $this->router     = $router;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle package.
     *
     * @param string $data Package data
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function handle(string $data, $client) : void
    {
        $request = new Request($data);
        $request->getHeader()->setAccount($client->getAccount()->getId());

        $response = new Response();

        $this->dispatcher->dispatch(
            $this->router->route($data, 'Socket', 1, $client->getAccount()),
            $request,
            $response
        );
    }
}
