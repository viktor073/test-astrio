<?php




/**
 *  Connect for Database
 */
class Connect
{
	protected PDO $pdo;

	protected $fetchMode = PDO::FETCH_ASSOC;


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

	public function setFetchMode(array $fetchMode): void
	{
		$this->fetchMode = $fetchMode;
	}

	protected function execute(string $sql, ?array $params = null)
	{
		$statement = $this->pdo->prepare($sql);
		$statement->setFetchMode($this->fetchMode);
		$statement->execute($params);

		return $statement;
	}

	public function fetch(string $sql, array $params, ?array $fetchMode = null)
	{
		var_dump($sql, $params);
		return $this->execute($sql, $params)->fetch($fetchMode);
	}

	public function fetchAll(string $sql, ?array $params = null, ?array $fetchMode = null)
	{
		return $this->execute($sql, $params)->fetchAll($fetchMode);
	}

	public function fetchColumn(string $sql, ?array $params = null, ?int $column = null)
	{
		return $this->execute($sql, $params)->fetchColumn($column);
	}

	public function fetchObject(string $sql, ?array $params = null, ?string $className, ?array $ctor_args)
	{
		return $this->execute($sql, $params)->fetchColumn($className, );
	}

	public function insert(string $sql, array $params)
	{
		return $this->execute($sql, $params);
	}

}
