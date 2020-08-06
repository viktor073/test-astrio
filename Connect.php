<?php

/**
 *  Connect for Database
 */
class Connect
{
	protected PDO $pdo;

	protected $fetchMode = PDO::FETCH_ASSOC;

	/**
	 * [init Connect]
	 */
	public function __construct()
	{
		$config = ['driver' => 'mysql',
		        'host' => '127.0.0.1',
		        'port' => 5432,
		        'user' => 'root',
		        'password' => '',
		        'dbname' => 'astrio'];

		$dsn = "{$config['driver']}:dbname={$config['dbname']};host={$config['host']}";

		$this->pdo = new PDO($dsn, $config['user'], $config['password']);
	}

	/**
	 * [set Fetch Mode]
	 * @param array $fetchMode [description]
	 */
	public function setFetchMode(array $fetchMode): void
	{
		$this->fetchMode = $fetchMode;
	}

	/**
	 * [query SELECT execution]
	 * @param  string     $sql
	 * @param  array|null $params
	 * @return [PDOStatement]
	 */
	protected function executeGet(string $sql, ?array $params = null): PDOStatement
	{
		$statement = $this->pdo->prepare($sql);
		$statement->setFetchMode($this->fetchMode);
		$statement->execute($params);

		return $statement;
	}

	/**
	 * [query INSERT, UPDATE, DELETE execution]
	 * @param  string     $sql
	 * @param  array|null $params
	 * @return [bool]
	 */
	protected function executeSet(string $sql, ?array $params = null): bool
	{
		$statement = $this->pdo->prepare($sql);
		$statement->setFetchMode($this->fetchMode);
		return $statement->execute($params);
	}

	/**
	 * [fetch PDOStatement]
	 * @param  string     $sql
	 * @param  array      $params
	 * @param  array|null $fetchMode
	 * @return [mixed]
	 */
	public function fetch(string $sql, array $params, ?array $fetchMode = null)
	{
		return $this->executeGet($sql, $params)->fetch($fetchMode);
	}

	/**
	 * [fetchAll PDOStatement]
	 * @param  string     $sql
	 * @param  array|null $params
	 * @param  array|null $fetchMode
	 * @return [array]
	 */
	public function fetchAll(string $sql, ?array $params = null, ?array $fetchMode = null): array
	{
		return $this->executeGet($sql, $params)->fetchAll($fetchMode);
	}

	/* [fetchColumn PDOStatement]
	 * @param  string     $sql
	 * @param  array|null $params
	 * @param  array|null $fetchMode
	 * @return [mixed]
	 */
	public function fetchColumn(string $sql, ?array $params = null, ?int $column = null)
	{
		return $this->executeGet($sql, $params)->fetchColumn($column);
	}

	/**
	 * [fetchObject PDOStatement]
	 * @param  string     $sql
	 * @param  array|null $params
	 * @param  string     $className
	 * @param  array      $ctor_args
	 * @return [mixed]
	 */
	public function fetchObject(string $sql, ?array $params = null, ?string $className, ?array $ctor_args)
	{
		return $this->executeGet($sql, $params)->fetchColumn($className, );
	}

	/**
	 * [insert PDOStatement]
	 * @param  string $sql    [description]
	 * @param  array  $params [description]
	 * @return [bool]         [description]
	 */
	public function insert(string $sql, array $params): bool
	{
		return $this->executeSet($sql, $params);
	}

}
