<?php
/**
*.class.weebo.install.php - WEEBO framework lib.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
* --
*
* @package System
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2012-04-09)
* @link 
*/

class WeeboInstall{

public $installed, $coreInstalled, $errors, $onInit;

protected $moduleConfig, $dirMask;

final public function __construct() { 
	$this->installed = true;
	$this->onInit = true;
	$this->coreInstalled = true;
	$this->errors = array();
	$this->moduleConfig = null;
	$this->dirMask = '0777';
}

final public function __clone() { throw new WeeboException("Cannot clone this class!"); }

public function run(){
	
	if( ini_get('safe_mode') ){
		$this->errors[] = 'WARNING, PHP SAFE MODE IS NOT SUPPORTED';
		$this->installed = false;
		$this->coreInstalled = false;
	}else{
		if(defined('_INSTALLCHECK_') && _INSTALLCHECK_ === true)
		{
			$engine = _SYSTEMDBDRIVER_ == 'Mysqldb' ? 'mysql': 'postgresql';
			
			$testEngine = Db::test_connection();
			$testDb = false;
			
			if($testEngine !== false)
			{
				$testDb = Db::test_db();
			}
			
			// TEST DB CONNECTION
			if($testEngine === false){
				$this->coreInstalled = false;
				$this->errors[] = 'SQL CONNECTION ERROR: CHECK CONFIGURATION';
			}
			
			// TEST DB EXISTS
			if($testDb === false){
				$this->coreInstalled = false;
				$this->errors[] = 'SQL DATABASE ERROR: CHECK CONFIGURATION';
			}
			
			// TEST GD LIB
			if (!extension_loaded('gd') || !function_exists('gd_info')) {
				$this->coreInstalled = false;
				$this->errors[] = 'GD ERROR: GD INSTALLATION REQUIRED';
			}
			
			// TEST MEMCACHE, IF ENABLED IN config.php 
			if(_MEMCACHEENABLED_ === true && !class_exists('Memcache')){
				$this->coreInstalled = false;
				$this->errors[] = 'MEMCACHE ERROR: MEMCACHE INSTALLATION REQUIRED, INSTALL IT OR DISABLE THIS OPTION IN config.php';
			}
			
			$this->systemDefaults();
			
			if($this->coreInstalled === true && $this->onInit === false){
				$this->moduleInstallCheck();
			}else{
				$this->installed = false;
			}
		}
	}
}

public function displayErrors(){
	
	$html = null;
	
	foreach($this->errors as $e){
		$html .= '<p>'.$e.'</p>';
	}
	return $html;
}

protected function systemDefaults(){
	
	$defaultDirs = array(
		_GLOBALDATADIR_,
		_GLOBALCACHEDIR_,
		'userdata'
	);
	
	foreach($defaultDirs as $dir){
		if( file_exists($dir) && is_dir($dir) ){
			$perm = Storage::permission($dir);
			
			if($perm != $this->dirMask){
				$this->coreInstalled = false;
				$this->errors[] = 'PERMISSION ERROR: '.$dir.' ['.$perm.']';
			}
		}else{
			$this->coreInstalled = false;
			$this->errors[] = 'DIR ERROR: '.$dir;
		}
	}
}

protected function moduleInstallCheck(){
	
	$modules = Registry::get('moduledata');
	
	foreach( $modules as $m => $data ){
		
		$this->moduleConfig = null;
		
		$installFile = Registry::get('serverdata/root').'/mwms/modules/'.$m.'/install.xml';
		if( file_exists($installFile) ){
			$this->moduleConfig = simplexml_load_file($installFile);
			
			$this->runSQLCheck();
			$this->runDirCheck();
			//$this->runMethods();
			
		}else{
			$this->installed = false;
			$this->errors[] = 'INSTALL FILE ERROR: '.$installFile;
		}
	}
}

protected function runSQLCheck(){
	
	$engine = _SYSTEMDBDRIVER_ == 'Mysqldb' ? 'mysql': 'postgresql';
	
	if( is_object( $this->moduleConfig->sql ) )
	{
		$sql = $this->moduleConfig->sql->$engine;
		
		if( is_object( $sql ) && is_object( $sql->table ) )
		{
			foreach( $sql->table as $table ){
				
				$tableName = $table->attributes()->name;
				
				if($engine == 'mysql')
				{
					$chkQ = "SHOW TABLES LIKE '"._SQLPREFIX_."_".$tableName."'";
				}else{
					$chkQ = "SELECT * FROM pg_catalog.pg_tables WHERE schemaname LIKE 'public' AND tablename LIKE '"._SQLPREFIX_."_".$tableName."' ";
				}
				$checkTable = Db::result($chkQ);
				
				if(count($checkTable) == 1)
				{
					//echo 'TABLE '._SQLPREFIX_."_".$tableName.' OK';
				}else{
					$this->errors[] = 'SQL TABLE ERROR: '.$tableName;
					
					foreach( $table->query as $queryTemplate ){
						$q = str_replace('%SQLPREFIX%', _SQLPREFIX_, $queryTemplate);
						Db::query($q);
					}
				}
				
			}
			
			foreach( $sql->table as $table ){
				
				$tableName = $table->attributes()->name;
				if($engine == 'mysql')
				{
					$chkQ = "SHOW TABLES LIKE '"._SQLPREFIX_."_".$tableName."'";
				}else{
					$chkQ = "SELECT * FROM pg_catalog.pg_tables WHERE schemaname LIKE 'public' AND tablename LIKE '"._SQLPREFIX_."_".$tableName."' ";
				}
				$checkTable = Db::result($chkQ);
				
				if(count($checkTable) == 1)
				{
					
				}else{
					$this->errors[] = 'SQL TABLE ERROR: '.$tableName;
					$this->installed = false;
				}
				
			}
			
		}
	}
} 

protected function runDirCheck(){
	
	$dirs = $this->moduleConfig->fs;
	$modinst = true;
	
	if( is_object($dirs) && is_object($dirs->dir) )
	{
		foreach($dirs->dir as $dir){
			if( file_exists($dir) && is_dir($dir) ){
				$perm = Storage::permission($dir);
				
				if($perm != $this->dirMask){
					$this->installed = $modinst = false;
					$this->errors[] = 'PERMISSION ERROR: '.$dir.' ['.$perm.']';
				}
			}else{
				$this->installed = $modinst = false;
				$this->errors[] = 'DIR ERROR: '.$dir;
			}
		}
		
		if( $modinst === false )
		{
			foreach($dirs->dir as $dir){
				
				@Storage::makeDir($dir);
				
				if( file_exists($dir) && is_dir($dir) ){
					$perm = Storage::permission($dir);
					
					if($perm != $this->dirMask){
						$this->installed = false;
						$this->errors[] = 'PERMISSION ERROR: '.$dir.' ['.$perm.']';
					}
				}else{
					$this->installed = false;
					$this->errors[] = 'DIR ERROR: '.$dir;
				}
			}
		}
		
	}
} 


}
?>
