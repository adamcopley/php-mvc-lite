<?php defined('SYSPATH') or die('No direct script access');
class Database
{
	private $_config = array();
    private $_db;
    private $_query;
    private $_result;

    public function __construct($config = NULL)
	{
		if(!$config)
		{
			$this->_config = Config::get('database');
		}
		else
		{
			$this->_config = $config;
		}

      	$this->_connect();
    }

    public function __destruct()
	{
      	$this->_disconnect();
    }

    private function _connect()
	{
      	try
	  	{
        	$this->_db = new PDO($this->_config['dsn'], $this->_config['user'], $this->_config['pass']);
        	$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
      	}
	  	catch(PDOException $e)
	  	{
        	echo $e->getMessage();
      	}
    }

    private function _disconnect()
	{
      	$this->_query = NULL;
      	$this->_result = NULL;
      	$this->_db = NULL;
    }

    public function query($query)
	{
      	if(!$this->_db)
	  	{
        	$this->_connect();
      	}

      	$this->_query = $this->_db->prepare($query);

      	return $this;
    }

    public function bind($param, $value)
	{
      	$this->_query->bindParam($param, $value);

      	return $this;
    }

    public function execute($params = NULL)
	{
      	if(is_array($params))
		{
        	return $this->_query->execute($params);
      	}
		else
		{
        	return $this->_query->execute();
      	}
    }

    public function fetch_array_all($params = NULL)
	{
      	$this->execute($params);

      	$this->_result = $this->_query->fetchAll(PDO::FETCH_ASSOC);

      	return $this->_result;
    }

	public function getColumnNames($table)
	{
		//uses PDO object directly to access columnCount and getColumnMeta methods
		$rs = $this->_db->query('SELECT * FROM ' . $table . ' LIMIT 0');

		$columns = array();

		for($i = 0; $i < $rs->columnCount(); $i++)
		{
			$col = $rs->getColumnMeta($i);
			$columns[] = $col['name'];
		}
		return $columns;
	}
}
