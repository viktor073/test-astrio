<?php

require_once "MysqlQueryBuilder.php";

/**
 *  interface Box
 */
interface InterfaceBox
{
	public function setData(string $key, $value): bool;

	public function getData(string $key);

	public function save();

	public function load();
}


/**
 *  abstract class
 */
abstract class AbstractBox implements InterfaceBox
{
	/**
	 * [$data store $key, $value]
	 * @var array
	 */
	protected array $data = [];

	/**
	 * [setData set $key, $value]
	 * @param [string] $key  [key for value]
	 * @param [bool] $value [value]
	 */
	public function setData(string $key, $value): bool
	{
		$this->data[] = [$key, $value];
		if (isset($this->data[$key])) {
			return true;
		}
		return false;
	}

	/**
	 * [getData get from this data]
	 * @param  [string] $key [key for value]
	 * @return [mixed]      [return mixed]
	 */
	public function getData(string $key)
	{
		if (($key = array_search($key, array_column($this->data, 0))) !== false) {
			return $this->data[$key][1];
		}
		return null;
	}

	/**
	 * [save abstract function]
	 */
	abstract public function save();

	/**
	 * [load abstract function]
	 */
	abstract public function load();
}

/**
 * class for save and load data from file
 */
class FileBox extends AbstractBox
{
	/**
	 * [$filename name file for gata]
	 * @var string
	 */
	protected $filename = "FileBox.txt";

	/**
	 * [save for file]
	 * @return [bool]
	 */
	public function save(): bool
	{
		touch($this->filename);

		return file_put_contents($this->filename, json_encode($this->data));
	}

	/**
	 * [load data form file]
	 * @return [type] [description]
	 */
	public function load(): bool
	{
		$this->data = json_decode(file_get_contents($this->filename));
		if (is_null($this->data)) {
			return false;
		}
		return true;
	}
}

/**
 * class for save and load data from data base
 */
class DbBox extends AbstractBox
{
	/**
	 * mysql query builder
	 */
	protected MysqlQueryBuilder $builder;

	/**
	 * [init DbBox and MysqlQueryBuilder]
	 */
	public function __construct()
	{
		$this->builder = MysqlQueryBuilder::table('data');
	}

	/**
	 * [save for data base]
	 * @return [bool]
	 */
	public function save(): bool
	{
		foreach ($this->data as $data) {
			if (!$this->builder->insert(['key_id', 'value'], $data)->set()) {
			 	return false;
			}
		}

		return true;
	}

	/**
	 * [load from data base]
	 */
	public function load()
	{
		foreach ($this->builder->all() as $fetch) {
			$this->data[] = [$fetch['key_id'], $fetch['value']];
		}
	}
}

/**
 * Box store
 */
class Box
{
	/**
	 * [$instance static param for singlton]
	 * @var Box or null
	 */
	protected static ?Box $instance = null;

	/**
	 * interface box
	 */
	protected InterfaceBox $interfaceBox;

	/**
	 * [init new box]
	 * @param InterfaceBox $interfaceBox
	 */
	protected function __construct(InterfaceBox $interfaceBox)
	{
		$this->interfaceBox = $interfaceBox;
	}

	protected function __wakeup()
	{
		//
	}

	protected function __sleep()
	{
		//
	}

	protected function __clone()
	{
		//
	}

	/**
	 * [init singlton box]
	 * @param  InterfaceBox $interfaceBox
	 * @return [Box]
	 */
	public static function init(InterfaceBox $interfaceBox): Box
	{
		self::$instance ??= new static($interfaceBox);

        return self::$instance;
	}

	/**
	 * [call function setData from interfaceBox]
	 * @param [string] $key   [description]
	 */
	public function setData(string $key, $value)
	{
		$this->interfaceBox->setData($key, $value);
	}

	/**
	 * [call function getData from interfaceBox]
	 * @param  [string] $key
	 * @return [mixed]
	 */
	public function getData(string $key)
	{
		return $this->interfaceBox->getData($key);
	}

	/**
	 * [call function save from interfaceBox]
	 */
	public function save()
	{
		$this->interfaceBox->save();
	}

	/**
	 * call function load from interfaceBox
	 */
	public function load()
	{
		$this->interfaceBox->load();
	}
}

/**
 * [client code]
 */
$box = Box::init(new DbBox);
$box->setData("1", "11111");
$box->setData("2", "222222");
$box->setData("3", "3333");
$box->setData("4", "44444");
 var_dump($box->getData("4"));
$box->save();
//var_dump($box->getData("2"));
$box->load();
var_dump($box->getData("2"));
var_dump($box->getData("4"));