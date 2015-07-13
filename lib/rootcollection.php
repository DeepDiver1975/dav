<?php

namespace OCA\DAV;

use OC\Connector\Sabre\Principal;
use Sabre\CalDAV\Backend\PDO;
use Sabre\CalDAV\CalendarRoot;
use Sabre\CalDAV\Principal\Collection;
use Sabre\DAV\SimpleCollection;

class RootCollection extends SimpleCollection {

	private $principalBackend;

	public function __construct() {
		$children = [];

		$this->initPrincipals($children);

		$children[] = new Files\RootCollection();
		$children[] = new Upload\RootCollection();
		$children[] = new Thumbnail\RootCollection();
		$children[] = new SimpleCollection('blocks');

		$this->initCalendar($children);

		parent::__construct('root', $children);
	}

	private function initPrincipals(array &$children) {
		$this->principalBackend = new Principal(
			\OC::$server->getConfig(),
			\OC::$server->getUserManager()
		);
		$principalCollection = new Collection($this->principalBackend);
//		$principalCollection->disableListing = true;
		$children[] = $principalCollection;
	}

	private function initCalendar(&$children) {
		$backend = new PDO($this->getDatabase());
		$node    = new CalendarRoot($this->principalBackend, $backend);
		$children[] = $node;
	}

	/**
	 * @return \PDO
	 */
	private function getDatabase() {
		/** @var \Doctrine\DBAL\Connection $conn */
		$conn = \OC::$server->getDatabaseConnection();
		return $conn->getWrappedConnection();
	}

}
