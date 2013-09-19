<?php
Registry::set('mediatree', array());

$mf = new MediaDisplay;

$mf->rootDir = _GLOBALDATADIR_;

$dir = isset($_POST['dir']) ? $_POST['dir']: $mf->rootDir;

echo $mf->directoryRead(System::autoUTF($dir));

//System::dump(Registry::get('mediatree'));
?>
