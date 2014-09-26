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

function hideDOCviewer() {
	$('#content table').show();
	$("#controls").show();
	$("#editor").show();
	$('iframe').remove();
	$('a.action').remove();
}

showPreview.oldCode='';
showPreview.lastTitle='';
var oldcontent = '';

function showPreview(dir,filename, type){
		
	var baseUrl = '';
	if(dir === '/'){
		baseUrl = dir + filename;
	}
	else{
		baseUrl = dir + "/" + filename;
	}
	
	//make an ajax call to get the path and then feed the path to the popup
	$.ajax({
        url: OC.Router.generate('preview_handler', { job: 'getName', fname: baseUrl}),
        type: 'get',
        dataType: 'text',
        success: function(data) {
        	var viewer = OC.Router.generate('preview_handler', { job: 'onClick', fname: data});
        	//here, change the url
        	var rootLink = OC.linkTo('file_previewer', 'preview_handler.php');
        	rootLink = rootLink.substring( 0, rootLink.lastIndexOf( "/" ) + 1);
        	window.history.pushState("","", rootLink + "preview" + baseUrl);
        	$.fancybox({
                'autoScale'     : false,
                'transitionIn'  : 'none',
                'transitionOut' : 'none',
                'title'         : this.title,
                'width'     : '75%',
                'height'        : '75%',
                'href'          : viewer,
                'type'          : 'iframe'
            });
        },
        error: function(data) {
            OC.Notification.show(data.statusText);
            hideNotification(3000);
        }
    });
		
}

/*$(window).on("hashchange", function() {
    if (!/#preview/.test(window.location.hash)) {
    	$('#content').html(oldcontent);
		$("#editor").show();
		$('#content table').show();
		//$("#controls").show();
		oldcontent = '';
    }
  });*/

function getFilePath(dir, filename, prev_type) {
	var baseUrl = '';
	if(dir === '/'){
		baseUrl = dir + '_html/' + filename + '/index.html';	
	}
	else{
		baseUrl = dir + '/_html/' + filename + '/index.html';
	}
	var viewer = OC.Router.generate('preview_handler', { fname: baseUrl});
	//var viewer = OC.linkTo('file_previewer', 'docViewer.php')+'?fname='+baseUrl+'&type='+prev_type;
	return viewer;
}

function getRequestURL(dir, filename, type) {
	var baseUrl = '';
	if(dir === '/'){
		baseUrl = dir + filename + '/';	
	}
	else{
		baseUrl = dir + '/' + filename + '/';
	}
	var idx = filename.lastIndexOf(".");
	var url = baseUrl + filename.slice(0, idx) + type;
	var viewer = OC.Router.generate('previewer', { fname: url});
	return viewer;
}

$(document).ready(function() {
	
	$.ajax({
		url: OC.linkTo('file_previewer', 'preview_handler.php')+'?job=show_preview', 
		type: 'get',
		dataType: 'json',
		success: function(data) {
			if(data['preview'] === true){
				showPreview(data['dir'], data['filename'], 'html');
			}
		},
		error: function(data) {
			OC.Notification.show(data.statusText);
			hideNotification(3000);
		}
	});
	
	//if(location.href.indexOf("files")!=-1) {
		if(typeof FileActions!=='undefined'){
			var supportedMimes = new Array(
				'text/plain',
				'text/markdown',
				'image/tiff',
				'image/tiff-fx',
				'image/gif',
				'image/jpeg',
				'image/png',
				'application/msword',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
				'application/msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
				'application/mspowerpoint',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
				'application/vnd.openxmlformats-officedocument.presentationml.template',
				'application/vnd.openxmlformats-officedocument.presentationml.slide',
				'application/vnd.oasis.opendocument.text', 
				'application/vnd.oasis.opendocument.spreadsheet',
				'application/vnd.oasis.opendocument.graphics',
				'application/vnd.oasis.opendocument.presentation',
				'application/vnd.abf');
			for (var i = 0; i < supportedMimes.length; ++i){
				var mime = supportedMimes[i];
				FileActions.register(mime,'Preview',OC.PERMISSION_READ,'',function(filename){
					showPreview($('#dir').val(),filename, 'html');
				});
				/*FileActions.register(mime,'Prev',OC.PERMISSION_READ,'',function(filename){
					showPreview($('#dir').val(),filename, 'pdf');
				});
				FileActions.setDefault(mime,'Prev');*/
			}
		}
	//}
	
	//if(location.href.indexOf("files")!=-1) {
		if(typeof FileActions!=='undefined') {
			FileActions.register('application/msword','ePub', OC.PERMISSION_READ, '',function(filename) {
				//window.location = OC.linkTo('file_previewer', 'docViewer.php')+'?dir='+encodeURIComponent($('#dir').val()).replace(/%2F/g, '/')+'&file='+encodeURIComponent(filename.replace('&', '%26'))+'&type=epub';
				window.location = getRequestURL($('#dir').val(), filename, '.epub');
			});
		}
	//}
		
});
