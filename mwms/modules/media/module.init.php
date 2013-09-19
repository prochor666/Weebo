<?php
$mwms_module_init = array(
'auto_script' => array('media.load.php'),
'admin_script' => array('view/media.admin.php'),
'icon' => array('img/folder_process.png'),
'js' => array('js/media.core.js', '../../../shared/jwplayer/jwplayer.js', '../../../shared/plupload/js/plupload.full.js', '../../../shared/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js'),
'css' => array('css/media.core.css', 'css/file.types.css', '../../../shared/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css'),
'lng_dir' => array('lng'),
'lib_dir' => array('lib'),
'playlist' =>  array('video', 'audio','image'),
'video' => array('avi','mpg','mpeg','divx','mov','vob','ogv','mp4','flv','m4v','webm', 'swf','video','wmv','vob','bup','ifo','sub','idx','srt'),
'image' => array('jpg','jpeg','png','bmp','gif','tga'),
'audio' => array('mp3','mpa','ogg','wav'),
'ascii' => array('txt','pps','sub','srt','ini','nfo','conf','inf','php','htaccess', 'xml','html','js','htm','asp','aspx','phps','lst','md5','css','datacache','log','pid','progress'),
'doc' => array('doc','xls','docx','xlsx','odt','ods','rtf','ppt','pps','pps'),
'pdf' => array('pdf'),
'url' => array('url'),
'embed' => array('embed'),
'archive' => array('zip','rar','7z','tar.gz','gz','tar','arj','tbz','tgz'),
'exec' => array('exe', 'bat', 'cmd', 'sh'),

'protectedFiles' => array('.htaccess', '.htpasswd'),
'protectedDirs' => array('.'),
);
?>
