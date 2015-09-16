<?php

namespace OCA\DAV\Thumbnails;

use OC\Connector\Sabre\Directory;
use OC\Files\Filesystem;
use OC\Files\View;
use OC\Preview;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\ICollection;
use Sabre\HTTP\URLUtil;

class Home implements ICollection {

	/**
	 * Home constructor.
	 *
	 * @param array $principalInfo
	 */
	public function __construct($principalInfo) {
		$this->principalInfo = $principalInfo;
	}

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
		list(,$name) = URLUtil::splitPath($this->principalInfo['uri']);
		return $name;
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
		$user = $this->getName();
		Filesystem::initMountPoints($user);
		if (!$rootView->file_exists('/' . $user . '/thumbnails')) {
			$rootView->mkdir('/' . $user . '/thumbnails');
		}
		$view = new View('/' . $user . '/thumbnails');
		$rootInfo = $view->getFileInfo('');
		$impl = new Directory($view, $rootInfo);
		return $impl;
	}
}
