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

//add 3rdparty folder to include path
$dir = dirname(dirname(__FILE__)).'/3rdparty';
set_include_path(get_include_path() . PATH_SEPARATOR . $dir);

//load the required files
OCP\Util::addscript('file_previewer', 'loader');
OCP\Util::addScript('file_previewer', 'jquery.fancybox.pack');
OCP\Util::addStyle('file_previewer', 'jquery.fancybox');

OC::$CLASSPATH['Apache_Solr_Service'] = 'apps/file_previewer/3rdparty/SolrPhpClient/Apache/Solr/Service.php';
OC::$CLASSPATH['OCA\file_previewer\lib\Solr'] = 'apps/file_previewer/lib/solr.php';

//create the configuration file in data directory with default values
$config_file = \OC::$SERVERROOT.'/data/file_previewer_config.json';
if(!file_exists($config_file)){
	$fp = fopen($config_file, 'x');
	$entry = array("fascinator" => array("downloadURL" => "http://localhost:9997/portal/default/download/",
								"solr" => array("host" => "localhost", "port" => 9997, "path" => "/solr/fascinator/")));
	fwrite($fp, json_encode($entry));
	fclose($fp);
}
