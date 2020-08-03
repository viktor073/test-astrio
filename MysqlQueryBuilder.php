<?php

require_once "Connect.php";

/**
 * Builder query MySQL
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

	protected function __construct(string $table)
	{
		$this->table = $table;
		$this->connect = new Connect;
	}

	public static function table(string $table)
	{
		return new MysqlQueryBuilder($table);
	}

	public function select(array $selects = [' * '])
	{
		$this->selects = $selects;
		return $this;
	}

	public function where(string $field, string $operator = '=', $value = null)
	{
		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$this->wheres[] = [[$field, $operator], $value];

		return $this;
	}

	public function limit(int $start, int $offset)
	{
		$this->limits[] = ['start' => $start, 'offset' => $offset];

		return $this;
	}

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

	protected function getInsertSql(): string
	{
		$sql = "INSERT INTO " . $this->table . ' (' . implode(', ', $this->into) .
			")  VALUES  (?" . str_repeat(', ?', count($this->values)-1) . ")";

		return $sql;
	}

	public function insert(array $into, array $values)
	{

		$this->into = $into;
		$this->values = $values;

		return $this;
	}

	public function get()
	{
		return $this->connect->fetch($this->getSelectSql(), array_column($this->wheres, 1));
	}

	public function all()
	{
		return $this->connect->fetchAll($this->getSelectSql(), array_column($this->wheres, 1));
	}

	public function set()
	{
		return $this->connect->insert($this->getInsertSql(), array_values($this->values));
	}
}