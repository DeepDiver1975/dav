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


namespace OCA\DAV\Avatar;


use OCA\DAV\IPublicNode;
use OCP\IAvatar;
use Sabre\DAV\File;

class AvatarNode extends File implements IPublicNode {

	/**
	 * AvatarNode constructor.
	 *
	 * @param integer $size
	 * @param string $ext
	 * @param IAvatar $avatar
	 */
	public function __construct($size, $ext, $avatar) {
		$this->size = $size;
		$this->ext = $ext;
		$this->avatar = $avatar;
	}

	/**
	 * Returns the name of the node.
	 *
	 * This is used to generate the url.
	 *
	 * @return string
	 */
	function getName() {
		return "$this->size.$this->ext";
	}

	function get() {
		$image = $this->avatar->get($this->size);
		$res = $image->resource();

		ob_start();
		if ($this->ext === 'png') {
			imagepng($res);
		}
		imagejpeg($res);

		return ob_get_clean();
	}

}
