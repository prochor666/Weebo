<?php
/* System SQL */
define("_SQLPREFIX_", "sport5");							// System tables prefix... "._SQLPREFIX_."_table

/* MySQL */
define("_HOST_", "localhost");								// MySQL server
define("_MYDB_", "c1sport5_cz");								// MySQL database name
define("_USER_", "c1sport5_cz");								// MySQL user
define("_PASS_", "Nebul@931");									// MySQL password

/* PostgreSQL */
define("_PGHOST_", "localhost");							// PostgreSQL server
define("_PGMYDB_", "mydb");									// PostgreSQL database name
define("_PGUSER_", "postgres");								// PostgreSQL user
define("_PGPORT_", "5432");									// PostgreSQL port
define("_PGPASS_", "pwd");									// PostgreSQL password

/* MEMCACHE */
define("_MEMCACHEENABLED_", true);							// MEMCACHE enabled
define("_MEMCACHESERVER_", "localhost");					// MEMCACHE server, default localhost
define("_MEMCACHEPORT_", 11211);							// MEMCACHE port number, default 11211

/* Dbdriver */
define("_SYSTEMDBDRIVER_", "Mysqldb");						// class name with DB engine ['Postgresqldb','Mysqldb'] 

/* Install check */
define("_INSTALLCHECK_",true);								// Force Weebo installer ON/OFF

/* Autorun module */
define("_MODULEAUTORUN_", "cms");							// Module Jedi power: module Id/null

/* Website */
define("_WEEBOSITEURL_", "http://".$_SERVER['HTTP_HOST']);	// Website url
define("_WEEBOSITETITLE_", "Weebo");						// Website default title
define("_WEEBODEFAULTLNG_", "cs");							// web default language
define("_WEEBODEFAULTADMINLNG_", "cs");						// web default admin language

/* Pager + extension */
define("_WEEBOLIMITEDPAGER_", true);						// pager limited?
define("_WEEBOPAGERLIMITER_", 5);							// pager limited by num
define("_WEEBO_REWRITE_", true);							// rewrite enbaled?
define("_WEEBO_REWRITE_EXTENSION_", ".html");				// rewrite extension .html, .php... htaccess modification needed!

/* Images */
define("_IMAGEXSIZE_", 1024);								// gallery image width in pixels
define("_IMAGEYSIZE_", 768);								// gallery image height in pixels
define("_THUMBXSIZE_", 125);								// thumbnail width in pixels
define("_THUMBYSIZE_", 125);								// thumbnail height in pixels
define("_IMAGEQUALITY_", 100);								// jpeg image quality
define("_IMAGEMAXFILESIZE_", 1048576);						// max image size in bytes
define("_IMAGERESAMPLEORIGINAL_", true);					// Use original picture size [' true / false ']

/* Image watermark */
define("_DEFAULTWATERMARK_", null); 						// watermark image

/* Advancedconfig start !!! */
define("_WEEBOAJAXREQUESTCATCHER_", "live/live.php");
define("_WEEBOAJAXREQUESTTIMEOUT_", 10000);

/* Module autorun */
define("_MODULE_AUTORUN_", null);							// special feature, input direcotry [module ID] or null for dashboard, !!! deprecated !!!

/* Mail server config */
define("_ADMINMAIL_","info@domain.tld");

define("_USERLOGINFIELD_","mail");							// an existing mysql value for user login and registration username / form table _USERLOGINTABLE_ 
define("_USERLOGINTABLE_","_users");						// user login table admin/web
define("_USERPASSWORDLENGTH_", 4);							// user password security length

define("_GLOBALDATADIR_", "content");						// website files relative path "/some/path/" the real path is ../your setting
define("_GLOBALCACHEDIR_", "cache");						// website files relative path "/some/path/" the real path is ../your setting

define("_AUTOLOGOUT_", 2592000);							// Logout after xxx seconds

/* Advancedconfig start !!! */
define("_PRODUCTNAME_", "WEEBONX");
define("_PRODUCTVERSION_", "2013.07.08");					// version YEAR.MONTH.DAY of release (suffix)
define("_PRODUCTCODENAME_", "Nibiru");						// Code name 

define("_FILESYSTEMSLASH_", "/");							// WEEBO 5/6 compat
?>
