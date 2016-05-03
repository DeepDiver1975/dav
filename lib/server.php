<?php

namespace OCA\DAV;

use OC\Connector\Sabre\BlockLegacyClientPlugin;
use OCA\DAV\Auth\Auth;
use OCA\DAV\Files\CustomPropertiesBackend;
use OCP\IRequest;
use Sabre\DAV\Auth\Plugin;
use Sabre\HTTP\Util;

class Server {

	/** @var IRequest */
	private $request;

	public function __construct(IRequest $request, $baseUri) {
		// test PR
		$this->request = $request;
		$this->baseUri = $baseUri;
		$root = new RootCollection();
		$this->server = new \OC\Connector\Sabre\Server($root);

		// Backends
		$authBackend = new Auth();

		// Set URL explicitly due to reverse-proxy situations
		$this->server->httpRequest->setUrl($this->request->getRequestUri());
		$this->server->setBaseUri($this->baseUri);

		$this->server->addPlugin(new BlockLegacyClientPlugin(\OC::$server->getConfig()));
		$this->server->addPlugin(new Plugin($authBackend, 'ownCloud'));

		// wait with registering these until auth is handled and the filesystem is setup
		$this->server->on('beforeMethod', function () {
			// custom properties plugin must be the last one
			$user = \OC::$server->getUserSession()->getUser();
			if (!is_null($user)) {
				$this->server->addPlugin(
					new \Sabre\DAV\PropertyStorage\Plugin(
						new CustomPropertiesBackend(
							$this->server->tree,
							\OC::$server->getDatabaseConnection(),
							\OC::$server->getUserSession()->getUser()
						)
					)
				);
			}
		});
	}

	public function exec() {
		$this->server->exec();
	}
}
