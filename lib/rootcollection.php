<?php

namespace OCA\DAV;

use OC\Connector\Sabre\Principal;
use Sabre\CalDAV\Principal\Collection;
use Sabre\DAV\SimpleCollection;

class RootCollection extends SimpleCollection {

	public function __construct() {
		$principalBackend = new Principal(
			\OC::$server->getConfig(),
			\OC::$server->getUserManager()
		);
		$principalCollection = new Collection($principalBackend);
//		$principalCollection->disableListing = true;
		$avatarCollection = new Avatar\RootCollection($principalBackend);
//		$avatarCollection->disableListing = true;
		$filesCollection = new Files\RootCollection($principalBackend);
//		$filesCollection->disableListing = true;
		$thumbsCollection = new Thumbnails\RootCollection($principalBackend);
//		$thumbsCollection->disableListing = true;

		$children = [
			$principalCollection,
			$filesCollection,
			new Upload\RootCollection(),
			$thumbsCollection,
			new SimpleCollection('blocks'),
			$avatarCollection,
		];

		parent::__construct('root', $children);
	}

}
