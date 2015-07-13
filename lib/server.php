<?php

namespace OCA\DAV;

use OC\Connector\Sabre\Auth;
use OC\Connector\Sabre\BlockLegacyClientPlugin;
use OCP\IRequest;
use Sabre\CalDAV\ICSExportPlugin;
use Sabre\CalDAV\Plugin as CalDAVPlugin;
use Sabre\CalDAV\Schedule\Plugin as SchedulePlugin;
use Sabre\CalDAV\SharingPlugin;
use Sabre\DAV\Sync\Plugin as SyncPlugin;
use Sabre\DAV\Auth\Plugin;
use Sabre\HTTP\Util;

class Server {

	/** @var IRequest */
	private $request;

	public function __construct(IRequest $request, $baseUri) {
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
		$this->server->addPlugin(new \OC\Connector\Sabre\MaintenancePlugin(\OC::$server->getConfig()));
		$this->server->addPlugin(new \OC\Connector\Sabre\ExceptionLoggerPlugin('dav', \OC::$server->getLogger()));
		$this->server->addPlugin(new CalDAVPlugin());
		$this->server->addPlugin(new ICSExportPlugin());
		$this->server->addPlugin(new SchedulePlugin());
		$this->server->addPlugin(new SyncPlugin());
		$this->server->addPlugin(new SharingPlugin());
	}

	public function exec() {
		$this->server->exec();
	}
}
