<?php

namespace OCA\DAV\Zip;

use
    Sabre\DAV,
    Sabre\HTTP\RequestInterface,
    Sabre\HTTP\ResponseInterface;

class Plugin extends DAV\ServerPlugin {

    /**
     * Initializes the plugin. This function is automatically called by the server
     *
     * @param DAV\Server $server
     * @return void
     */
    function initialize(DAV\Server $server) {

        $server->on('method:POST',      [$this, 'httpPost']);

    }

    /**
     * Returns a plugin name.
     *
     * Using this name other plugins will be able to access other plugins
     * using DAV\Server::getPlugin
     *
     * @return string
     */
    function getPluginName() {
        return 'zip';
    }

    function httpPost(RequestInterface $request, ResponseInterface $response) {
        //
        // 1. get body
        // 2. save list of files to be zipped - json in the zip folder?
        //

    }

}
