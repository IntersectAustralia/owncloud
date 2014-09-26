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

session_start();
OCP\User::checkLoggedIn();
OCP\App::checkAppEnabled('file_previewer');

$file = isset($_GET['fname']) ? $_GET['fname'] : '';
$job = isset($_GET['job']) ? $_GET['job'] : '';

$user = OCP\User::getUser();

switch ($job) {
	case "getName":
		$name = getPreviewName($file);
		echo $name;
		break;
	case "onClick":
		showPerview($file);
		break;
	case "preview":
		//keep the action in the session
		$_SESSION['preview'] = true;
		$_SESSION['doc_name'] = $file;
		session_write_close();
		//redirect the user to home directory
		redirect($file);
		break;
	case "show_preview":
		if($_SESSION['preview'] == true){
			//tell browser that we need to show preview and send the filename as well
			$_SESSION['preview'] = false;
			$doc_name = $_SESSION['doc_name'];
			$parts = pathinfo($doc_name);
			//unset($_SESSION['doc_name']);
			//set dir and file name to the response
			session_write_close();
			$data = array('preview' => true, 'dir' => '/'.$parts['dirname'], 'filename' => $parts['basename']);
			echo json_encode($data);
		}
		break;
	default:
		;
		break;
}

function getPreviewName($file){
	
	$p_parts = pathinfo($file);
	$basename = $p_parts["basename"];
	$dir = $p_parts["dirname"];
	if($dir == '/'){
		$filename = $dir . "_html/" . $p_parts["basename"] . "/index.html";
	}
	else{
		$filename = $dir . "/_html/" . $p_parts["basename"] . "/index.html";
	}
	return $filename;
}

function redirect($file){
	$link = OCP\Util::linkTo("files", "index.php");
	$p_parts = pathinfo($file);
	$link = $link . "?dir=" . $p_parts["dirname"];
	header("Location: " . $link); /* Redirect browser */
	exit();
}

function showPerview($file){
	
	if (\OC\Files\Filesystem::isReadable($file)) {
		list($storage) = \OC\Files\Filesystem::resolvePath($file);
		if ($storage instanceof \OC\Files\Storage\Local) {
			$full_path = \OC\Files\Filesystem::getLocalFile($file);
			$current_content = file_get_contents($full_path);
			$inject = '<script type="text/javascript" src="/owncloud/apps/file_previewer/js/j5slide_embed.js"></script>';
			
			$val = preg_match('/<head>.*<\/head>/s',$current_content, $matches);
			if ($val) {
				$pattern = '/(<head>)(.*)(<\/head>)/s';
				$replacement = '$1$2' . $inject . '$3';
				$current_content = preg_replace($pattern, $replacement, $current_content);
			}
			echo $current_content;
			return;
		}
	} elseif (!\OC\Files\Filesystem::file_exists($file)) {
		//If this is a text file, show content
		$parent_dir = dirname($file);
		$parts = pathinfo($parent_dir);
		$f_name = substr_replace($parts['dirname'], $parts['basename'], -5);
		$f_path = \OC\Files\Filesystem::getLocalFile($f_name);
		$file_info = new finfo(FILEINFO_MIME_TYPE); 
		$mime_type = $file_info->buffer(file_get_contents($f_path));  
		
		if($mime_type == "text/plain") {
			$handle = fopen($f_path, "r");
			if ($handle) {
				while (($buffer = fgets($handle)) !== false) {
					echo $buffer . '<br/>';
				}
				if (!feof($handle)) {
					echo "Error: unexpected fgets() fail\n";
				}
				fclose($handle);
			}
			return;
		}
		else {
			header("HTTP/1.0 404 Not Found");
			$tmpl = new OC_Template('', '404', 'guest');
			$tmpl->assign('file', $name);
			$tmpl->printPage();
		}
	} else {
		header("HTTP/1.0 403 Forbidden");
		die('403 Forbidden');
	}
}
