<?php
/**
* test framwork for joomla components unit test
*/
error_reporting(E_ALL & ~E_NOTICE);
define( '_JEXEC', 1 );
define( '_UNITTEST', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
define('JPATH_BASE', 'adalogin');
define('JPATH_ROOT', 'adalogin');
define('JPATH_ADMINISTRATOR', 'adalogin/admin');

class testDataClass {
	/**
	* set input parameters for test
	* $inputs['name1'] = 'value1', $inputs['name2'] = 'value2', .... 
	*/
	protected $inputs;

	/**
	* set Database result, and errorNum, errorMsg for test
	* $dbResults[0] = JSON_encode('{'field1":"value1", "field2":"value2"}'); 
	* $dbResults[1] = JSON_encode([{'field1":"value1", "field2":"value2"}, {'field1":"value11", "field2":"value12"}])             
	* set $dbErrorNum, $dbErrorMsg
	*/
	protected $dbResults;
	protected $dbErrorNum;
	protected $dbErrorMsg;
	protected $dbIndex;

	public $mock_data = array();
	/**
	* set remoteCall results for test
	*/
	protected $remoteResults;
	protected $remoteIndex;
		
	function __construct() {
		$this->clear();
	}
	public function clear() {
		$this->inputs = array();
		$this->dbResults = array();
		$this->dbErrorNum = 0;
		$this->dbErrorMsg = '';
		$this->dbIndex = 0;
		$this->remoteResults = array();
		$this->remoteIndex = 0;
	}
	function addDbResult($value) {
		$this->dbResults[] = $value;
	}
	function setInput($name,$value) {
		$this->inputs[$name] = $value;
	}
	function addRemoteResult($value) {
		$this->remoteResults[] = $value;
	}
	public function getDbResult() {
		if ($this->dbIndex < count($this->dbResults))
		   $result = $this->dbResults[$this->dbIndex];
	    else
		   $result = '';	
		$this->dbIndex = $this->dbIndex + 1;
		return $result;
	}
	public function getRemoteResult() {
		if ($this->remoteIndex < count($this->remoteResults))
		   $result = $this->remoteResults[$this->remoteIndex];
	    else
		   $result = '';	
		$this->remoteIndex = $this->remoteIndex + 1;
		return $result;
	}
	public function getInput($name,$default='') {
		if (isset($this->inputs[$name]))
		  $result = $this->inputs[$name];
	    else
		  $result = $default;
		return $result;
	}
}

global $_SERVER;
global $testData;
global $componentName;
global $viewName;

global $testApplication;
global $testDocument;
global $testController;
global $testModel;
global $testView;
global $testDatabase;
global $testUser;

/**
* set component name for test (without 'com_')
*/
$componentName = 'valami';

class JFactory {
	public static function getApplication() {
		global $testApplication;
		if (!isset($testApplication)) $testApplication = new JApplication();
		return $testApplication;
	}
	public static  function getDocument() {
		global $testDocument;
		if (!isset($testDocument)) $testDocument = new JDocument();
		return $testDocument;
	}
	public static  function getUser($id=0) {
		global $testUser;
		if (!isset($testUser)) $testUser = new JUser();
		$testUser->id = $id;
		$testUser->username='testElek';
		return $testUser;
	}
	public static  function getLanguage() {
		return new JLanguage();
	}
	public static  function getDBO() {
		global $testDatabase;
		if (!isset($testDatabase)) $testDatabase = new JDatabase();
		return $testDatabase;
	}
}
class JApplication {
	public $input;
    function __construct() {
		$this->input = new JInput();
	}
	public function getUserStateFromRequest($name, $default='',$dataType='') {
		return $default;
	}	
	public function getCfg($name, $default='') {
		return $default;
	}
public function login($credentials) {
	return true;
}	
}
class JDocument {
	public function getType() {
		return 'html';
	}
}
class JInput {
	public function get($name, $default='') {
		global $testData;
		return $testData->getInput($name, $default);
	}
	public function set($name,$value,$dataType='') {
		global $testData;
		$testData->setInput($name,$value);
	}
}
class JRequest {
	public  static function getVar($name, $default='', $dataType='') {
		global $testData;
		return $testData->getInput($name, $default);
	}
	public  static function getWord($name, $default='', $dataType='') {
		return $this->getVar($name, $default, $dataType);
	}
	public  static function getCmd($name, $default='', $dataType='') {
		return $this->getVar($name, $default, $dataType);
	}
	public  static function setVar($name,$value,$dataType='') {
		global $testData;
		$testData->setInput($name,$value);
	}
}
class JURI {
	public  static function base() {
		return 'http://localhost/';
	}
	public  static function root() {
		return 'http://localhost/';
	}
}
class JText {
	public  static function _($token) {
		return $token;
	}
}
class JHTML {
	public  static function _($token) {
		return '<span class="html.token">'.$token.'</span>';
	}
}
class JDatabase {
	public function setQuery($sql) {
		
	}
	public function getQuery() {
		return '';
	}
	public function loadObjectList() {
		global $testData;
		return $testData->getDbResult();	
	}
	public function loadObject() {
		return $this->loadObjectList();
	}
	public function query() {
		return true;
	}
	public function getErrorNum() {
		return 0;
	}
	public function getErrorMsg() {
		return '';
	}
	public function quote($str) {
		if (is_numeric($str))
			return $str;
		else
			return '"'.$str.'"';
	}
}

class JUser {
	public $id = 0;
	public $username = '';
	public $name = '';
	public function save() {
		return true;
	}
	public function getParam($name) {
		return $name;
	}
	public function setParam($name,$value) {
		
	}
	public function bind($data) {
		return true;
	}
	public function getError() {
		return '';
	}
}

class JLanguage {
	
}
class JTable {
	protected $tableName;
	public function bind($data) {
		
	}
	public function getTableName() {
		return $this->tableName;
	}
	public function setError($str) {
		
	}
	public function getError() {
		
	}
}
class JControllerLegacy {
	protected $redirectURI = '';

	function __construct($config='') {}
	public function getView($aviewName = 'default',$viewType='html') {
		global $componentName, $viewName;
		$viewName = $aviewName;
		require_once (JPATH_COMPONENT.DS.'views'.DS.$viewName.DS.'view.'.$viewType.'.php');
		$viewClassName = $componentName.'View'.ucfirst($viewName);
		return new $viewClassName ();
	}
	public function getModel($modelName = '') {
		global $componentName,$viewName;
		if (!isset($this->_viewname)) $this->_viewname = '';
		if (($modelName == '') & ($this->_viewname != '')) $modelName = $this->_viewname;
		if (($modelName == '') & ($viewName != '')) $modelName = $viewName;
		$viewName = $modelName;
		require_once (JPATH_COMPONENT.DS.'models'.DS.$modelName.'.php');
		$modelClassName = $componentName.'Model'.ucfirst($modelName);
		return new $modelClassName ();
	}
	public function setRedirect($uri) {
	  $this->redirectURI = $uri;	
	}
	public function redirect($message = '') {
		global $testData;
        $testData->mock_data["redirectURI"] = $this->redirectURI;
		$testData->mock_data["redirectMsg"] = $message;
	}
	public function edit() {
		echo 'joomla default edit task';
	}
	public function add() {
		echo 'joomla default add task';
	}
	public function save() {
		echo 'joomla default save task';
	}
	public function remove() {
		echo 'joomla default remove task';
	}
	public function browse() {
		echo 'joomla default browse task';
	}
	public function setMessage($msg) {
		
	}
}
class JModelLegacy {
	public $_db;
	function __construct($config='') {
		$this->_db = new JDatabase();
	}
	public function set($name,$value) {
		$this->$name = $value;
	}
	public function getTable($tableName = '') {
		return new JTable();
	}
	public function getName() {
		global $viewName;
		return $viewName;
	}
	public function getQuery() {
		
	}
	public function getTotal() {
		
	}
	public function getItems() {
		
	}
	public function getItem($id) {
		
	}
	public function save($data) {
		
	}
	public function remove($data) {
		
	}
	public function check($data) {
		
	}
	public function canDelete($data) {
		
	}
	public function setError($str) {
		
	}
	public function getError() {
		
	}
	public function setState($name,$value) {
		
	}
	public function getState($name, $default='') {
		return $default;
	}
}
class JViewLegacy {
	protected $layout;
	function __construct($config='') {}
	public function set($name,$value) {
		$this->$name = $value;
	}
	public function setLayout($str) {
		$this->layout = $str;
	}
	public function display($tmp) {
		global $viewName;
		$tmp = $this->layout.$tmp;
		if ($tmp == '') $tmp = 'default';
		
		if ($this->layout != '')
		  echo 'testJoomlaFramwork view.display '.$this->layout.'_'.$tmp.'<br>';
		else	
		  echo 'testJoomlaFramwork view.display '.$tmp.'<br>';
		include JPATH_COMPONENT.DS.'views'.DS.$viewName.DS.'tmpl'.DS.$tmp.'.php';
	}
	public function setModel($model) {
		$this->model = $model;
	}
}

class JSession {
	public static function get($name, $default='') {
		return $default;
	}
	public static function set($name,$value) {
		
	}
	public static function checkToken() {
		return true;
	}
}

// global functions
function jimport($str) {}

// init globals
$_SERVER['HTTP_SITE'] = 'localhost';
$_SERVER['REQUEST_URI'] = 'index.php';
$componentName = 'testComponent';
$testData = new testDataClass();
?> 