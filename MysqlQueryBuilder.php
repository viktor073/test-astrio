<?php

require_once "Connect.php";

/**
 * query builder MySQL
 */
class MysqlQueryBuilder
{
	protected Connect $connect;

	protected string $table;
	protected array $selects = [' * '];
	protected array $wheres = [];
	protected array $limits = [];
	protected array $into = [];
	protected array $values = [];

	/**
	 * [init this class]
	 * @param string $table [name table from database]
	 */
	protected function __construct(string $table)
	{
		$this->table = $table;
		$this->connect = new Connect;
	}

	/**
	 * [table initializations in a static context]
	 * @param  string $table [name table from database]
	 * @return [MysqlQueryBuilder]
	 */
	public static function table(string $table): MysqlQueryBuilder
	{
		return new MysqlQueryBuilder($table);
	}

	/**
	 * [insert param query SELECT]
	 * @param  array  $selects [param query SELECT]
	 * @return [MysqlQueryBuilder]
	 */
	public function select(array $selects = [' * ']): MysqlQueryBuilder
	{
		$this->selects = $selects;
		return $this;
	}

	/**
	 *  [insert in array $wheres param query WHERE]
	 * @param  string $field
	 * @param  string $operator
	 * @param  [mixed] $value
	 * @return [MysqlQueryBuilder]
	 */
	public function where(string $field, string $operator = '=', $value = null): MysqlQueryBuilder
	{
		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$this->wheres[] = [[$field, $operator], $value];

		return $this;
	}

	/**
	 * [insert in array $limits param query LIMIT]
	 * @param  int    $start
	 * @param  int    $offset
	 * @return [MysqlQueryBuilder]
	 */
	public function limit(int $start, int $offset): MysqlQueryBuilder
	{
		$this->limits[] = ['start' => $start, 'offset' => $offset];

		return $this;
	}

	/**
	 * [get select query mysql]
	 * @return [string]
	 */
	protected function getSelectSql(): string
	{
		$sql = "SELECT " . implode(', ', $this->selects) . " FROM " . $this->table;

		if (sizeof($this->wheres)) {
			$sql .= " WHERE " . implode(' AND ', array_column($this->wheres, 0)) . " ? ";
		}

		if (sizeof($this->limits)) {
			$sql .= " LIMIT " . implode(',', $this->limits);
		}

		return $sql;
	}

	/**
	 * [get insert query mysql]
	 * @return [string]
	 */
	protected function getInsertSql(): string
	{
		$sql = "INSERT INTO " . $this->table . ' (' . implode(', ', $this->into) .
			")  VALUES  (?" . str_repeat(', ?', count($this->values)-1) . ")";

		return $sql;
	}

	/**
	 * [insert param for query mysql]
	 * @param  array  $into
	 * @param  array  $values
	 * @return [MysqlQueryBuilder]
	 */
	public function insert(array $into, array $values): MysqlQueryBuilder
	{

		$this->into = $into;
		$this->values = $values;

		return $this;
	}

	/**
	 * [call function fetch Connect]
	 * @return [mixed]
	 */
	public function get()
	{
		return $this->connect->fetch($this->getSelectSql(), array_column($this->wheres, 1));
	}

	/**
	 * [call function fetchAll Connect]
	 * @return [array]
	 */
	public function all(): array
	{
		return $this->connect->fetchAll($this->getSelectSql(), array_column($this->wheres, 1));
	}

	/**
	 * [call insert Connect]
	 * @return [bool]
	 */
	public function set()
	{
		return $this->connect->insert($this->getInsertSql(), array_values($this->values));
	}
}