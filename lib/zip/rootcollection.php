<?php

namespace OCA\DAV\Zip;

use OC\Connector\Sabre\Directory;
use OC\Files\Filesystem;
use OC\Files\View;
use OC\Preview;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\ICollection;

class RootCollection implements ICollection {

	function createFile($name, $data = null) {
		throw new Forbidden('Permission denied to create a file');
	}

	function createDirectory($name) {
		throw new Forbidden('Permission denied to create a folder');
	}

	function getChild($name) {
		return $this->impl()->getChild($name);
	}

	function getChildren() {
		return $this->impl()->getChildren();
	}

	function childExists($name) {
		return $this->impl()->childExists($name);
	}

	function delete() {
		throw new Forbidden('Permission denied to delete this folder');
	}

	function getName() {
		return 'zip';
	}

	function setName($name) {
		throw new Forbidden('Permission denied to rename this folder');
	}

	/**
	 * Returns the last modification time, as a unix timestamp
	 *
	 * @return int
	 */
	function getLastModified() {
		return $this->impl()->getLastModified();
	}

	/**
	 * @return Directory
	 */
	private function impl() {
		$rootView = new View();
		$user = \OC::$server->getUserSession()->getUser();
		Filesystem::initMountPoints($user->getUID());
		if (!$rootView->file_exists('/' . $user->getUID() . '/thumbnails')) {
			$rootView->mkdir('/' . $user->getUID() . '/thumbnails');
		}
		$view = new View('/' . $user->getUID() . '/thumbnails');
		$rootInfo = $view->getFileInfo('');
		$impl = new Directory($view, $rootInfo);
		return $impl;
	}
}
