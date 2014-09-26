<?php

/**
 * ownCloud - file_previewer App
 *
 * @author Lloyd Harischandra
 * @copyright 2014 University of Western Sydney www.uws.edu.au
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/*$this->create('previewer', '{fname}')
	->requirements(array('fname' => '.*'))
	->actionInclude('file_previewer/docViewer.php');*/

$this->create('preview_handler', '{job}/{fname}')
	->requirements(array('fname' => '.*'))
	->actionInclude('file_previewer/preview_handler.php');
