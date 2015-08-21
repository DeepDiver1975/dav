<?php
/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */


namespace OCA\DAV\Auth;


use OCA\DAV\IPublicNode;

class Auth extends \OC\Connector\Sabre\Auth {

	/**
	 * Authenticates the user based on the current request.
	 *
	 * If authentication is successful, true must be returned.
	 * If authentication fails, an exception must be thrown.
	 *
	 * @param \Sabre\DAV\Server $server
	 * @param string $realm
	 * @return bool
	 */
	function authenticate(\Sabre\DAV\Server $server, $realm) {
		if ($this->isPublicResource($server)) {
			return true;
		}
		return parent::authenticate($server, $realm);
	}

	/**
	 * Returns information about the currently logged in username.
	 *
	 * If nobody is currently logged in, this method should return null.
	 *
	 * @return string|null
	 */
	function getCurrentUser() {
		return parent::getCurrentUser();
	}

	public function isPublicResource(\Sabre\DAV\Server $server) {
		$uri = $server->getRequestUri();
		$node = $server->tree->getNodeForPath($uri);
		return ($node instanceof IPublicNode);
	}
}
