<?php
/**
* class.data.process.action.php - WEEBO framework lib.
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
* @package DataProcessAction
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-07-28)
* @link 
*/

class DataProcessAction{

public $input, $id, $allowsave;

protected $fieldName, $tableData, $lastInsert;

/*
 * Process form data with metadata extension
 *  
 * */
public function __construct(){
	$this->profileData = null;
	$this->metaData = null;
	$this->allowsave = false;
}

public function initAction(){

	$this->id = $this->input['id'];
	$this->AssignData = $this->input['AssignData'];

	$this->action = $this->input['action'];
	$this->fieldName = $this->input['fieldName'];
	$this->tableName = $this->input['tableName'];
	$this->metaUse = $this->input['metaUse'];
	$this->metaConnectId = $this->input['metaConnectId'];
	$this->metaDataTableName = $this->input['metaDataTableName'];
	$this->assignDataTableName = $this->input['assignDataTableName'];
	$this->assignTableName = $this->input['assignTableName'];
	$this->metaAssignId = $this->input['metaAssignId'];
	
	$this->doAction();
}


protected function doAction(){

	switch($this->action){
		case "assign":
			return $this->assignObject();
		case "unassign":
			return $this->unAssignObject();
		break;  case "del":
			return $this->delObject();
		break; default:
			return $this->blankObject();
	}

}

public function allowSave(){

	return $this->allowsave;
}

protected function assignObject(){

	$this->allowsave = true;
	foreach($this->id as $id){
		foreach($this->AssignData as $id_assign){
			if(!$this->isAssigned($id, $id_assign)){
				Db::query("INSERT INTO "._SQLPREFIX_.$this->assignTableName." SET ".$this->fieldName." = '".(int)$id."', ".$this->metaAssignId." = '".(int)$id_assign."' ");
			}
		}
	}
}

protected function unAssignObject(){

	$this->allowsave = true;
	foreach($this->id as $id){
		foreach($this->AssignData as $id_assign){
			if($this->isAssigned($id, $id_assign)){
				echo "DELETE FROM "._SQLPREFIX_.$this->assignTableName." WHERE ".$this->fieldName." = '".(int)$id."' AND ".$this->metaAssignId." = '".(int)$id_assign."' <br />";
				Db::query("DELETE FROM "._SQLPREFIX_.$this->assignTableName." WHERE ".$this->fieldName." = '".(int)$id."' AND ".$this->metaAssignId." = '".(int)$id_assign."' ");
			}
		}
	}
}

protected function isAssigned($id, $id_assign){

	$qq = Db::result("SELECT * FROM "._SQLPREFIX_.$this->assignTableName." WHERE ".$this->fieldName." = '".(int)$id."' AND ".$this->metaAssignId." = '".(int)$id_assign."' LIMIT 1");
	return count($qq) > 0 ? true: false;
}


protected function delObject(){
	$h= null;
	$this->allowsave = true;
	foreach($this->id as $id){
		
		Db::query("DELETE FROM "._SQLPREFIX_.$this->tableName." WHERE ".$this->fieldName." = '".(int)$id."' ");
		if($this->metaUse){ Db::query("DELETE FROM "._SQLPREFIX_.$this->metaDataTableName." WHERE ".$this->metaConnectId." = '".(int)$id."' "); }
		Db::query("DELETE FROM "._SQLPREFIX_.$this->assignTableName." WHERE ".$this->fieldName." = '".(int)$id."' ");
		/*
		$h .= "DELETE FROM "._SQLPREFIX_.$this->tableName." WHERE ".$this->fieldName." = '".(int)$id."' <br />";
		if($this->metaUse){ $h .= "DELETE FROM "._SQLPREFIX_.$this->metaDataTableName." WHERE ".$this->fieldName." = '".(int)$id."' <br />"; }
		$h .= "DELETE FROM "._SQLPREFIX_.$this->assignTableName." WHERE ".$this->fieldName." = '".(int)$id."' <br />";
		*/
	}

}

protected function blankObject(){

	return null;
}


}
?>
