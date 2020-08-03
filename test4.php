<?php

require_once "MysqlQueryBuilder.php";

/**
 *  interface Box
 */
interface InterfaceBox
{
	public function setData($key, $value);

	public function getData($key);

	public function save();

	public function load();
}


/**
 *  abstract class AbstractBox
 */
abstract class AbstractBox implements InterfaceBox
{
	protected array $data = [];

	public function setData($key, $value)
	{
		$this->data[] = [$key, $value];

		return true;
	}

	public function getData($key)
	{
		if (($key = array_search($key, array_column($this->data, 0))) !== false) {
			return $this->data[$key][1];
		}
		return null;
	}

	abstract public function save();

	abstract public function load();
}

/**
 * class FileBox
 */
class FileBox extends AbstractBox
{
	protected $filename = "FileBox.txt";

	public function save()
	{
		touch($this->filename);

		file_put_contents($this->filename, json_encode($this->data));

		return $this;
	}

	public function load()
	{
		$this->data = json_decode(file_get_contents($this->filename));
	}
}

/**
 * class FileBox
 */
class DbBox extends AbstractBox
{
	protected MysqlQueryBuilder $builder;

	public function __construct()
	{
		$this->builder = MysqlQueryBuilder::table('data');
	}

	public function save()
	{
		foreach ($this->data as $data) {
			$this->builder->insert(['key_id', 'value'], $data)->set();
		}

	}

	public function load()
	{
		foreach ($this->builder->all() as $fetch) {
			$this->data[] = [$fetch['key_id'], $fetch['value']];
		}
	}
}

/**
 * Box
 */
class Box
{
	protected static ?Box $instance = null;

	protected InterfaceBox $interfaceBox;

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

	public static function init(InterfaceBox $interfaceBox)
	{
		self::$instance ??= new static($interfaceBox);

        return self::$instance;
	}

	public function setData($key, $value)
	{
		$this->interfaceBox->setData($key, $value);
	}

	public function getData($key)
	{
		return $this->interfaceBox->getData($key);
	}

	public function save()
	{
		return $this->interfaceBox->save();
	}

	public function load()
	{
		return $this->interfaceBox->load();
	}
}

$box = Box::init(new FileBox);
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