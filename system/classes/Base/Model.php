<?php defined('SYSPATH') or die('No direct script access');
abstract class Base_Model
{
	const MODEL_PREFIX = 'Model_';

  	protected $_table = NULL;

	protected $_primary_key_field = 'id';

	protected $_primary_key = NULL;

	protected $_where_clauses = array();

	protected $_order_by = array();

	protected $_parameters = array();

	protected $_data = array();

	protected $_changed = array();

	protected $_columns = array();

	protected $_relations_many = array();

	protected $_relations_one = array();

	protected $_new_object = FALSE;

	public static function factory($model, $id = NULL, $data = NULL, $new_object = TRUE)
	{
		$model = self::MODEL_PREFIX . ucwords($model, '_');

		return new $model($id, $data, $new_object);
	}

  	public function __construct($id = NULL, $data = NULL, $new_object = TRUE)
  	{
		if(!$this->_table)
		{
			throw new Exception('The table name has not been defined for Model: ' . get_class($this) . '. Please define the protected property: "$_table".');
		}

    	if($id !== NULL)
    	{
			$this->_primary_key = $id;

			$this->_new_object = TRUE;

			return $this->find($this->_primary_key);
    	}

		if(is_array($data))
		{
			$this->_primary_key = $data[$this->_primary_key_field];

			//use the optional parameter here so that the find_all method can set the new_object property to FALSE when creating a new object for each row
			$this->_new_object = $new_object;

			$this->_setProps($data);

			return $this;
		}

		$this->_setBlankProps();

		$this->_new_object = TRUE;

		return $this;

  	}

	public function __set($column, $value)
	{
		if(array_key_exists($column, $this->_relations_many) || array_key_exists($column, $this->_relations_one))
		{
			$model = isset($this->_relations_one[$column]['model']) ? $this->_relations_one[$column]['model'] : $this->_relations_many[$column]['model'];

			$model = self::MODEL_PREFIX . ucwords($model, '_');

			throw new Exception('Cannot set property ' . $column . ' because it is a related model: ' . $model . '.');
		}

		if(!in_array($column, $this->_columns))
		{
			throw new Exception('The model: ' . get_class($this) . ' does not have the settable property: ' . $column . '.');
		}

		if(array_key_exists($column, $this->_data))
		{
			if($this->_data[$column] === $value)
			{
				return $this;
			}

			$this->_changed[$column] = $value;
		}

		$this->_data[$column] = $value;

		return $this;
	}

	public function __get($column)
	{
		if(array_key_exists($column, $this->_relations_many) && array_key_exists($column, $this->_relations_one))
		{
			throw new Exception('Both a \'many\' and a \'one\' relationship cannot be defined for the same model: ' . get_class($this) . '.');
		}

		if(array_key_exists($column, $this->_relations_many))
		{
			$modelname = self::MODEL_PREFIX . ucwords($this->_relations_many[$column]['model'], '_');

			$model = new $modelname();

			$model = $model->find_all($this->_data[$this->_relations_many[$column]], $this->_relations_many[$column]['foreign_key']);

			return $model;
		}

		if(array_key_exists($column, $this->_relations_one))
		{
			$modelname = self::MODEL_PREFIX . ucwords($this->_relations_one[$column]['model'], '_');

			$model = new $modelname();

			$model = $model->find($this->_data[$this->_relations_one[$column]], $this->_relations_one[$column]['foreign_key']);

			return $model;
		}

		if(!in_array($column, $this->_columns))
		{
			throw new Exception('The model: ' . get_class($this) . ' does not have the property: ' . $column . '.');
		}

		return $this->_data[$column];
	}

  	public function find($id = NULL, $column = NULL)
  	{
		if($this->loaded())
		{
			throw new Exception("Method find() cannot be called on a loaded object");
		}

		$sql = 'SELECT * FROM `' . $this->_table . '` ';

		$where = TRUE;

		if($id)
		{
			$this->_parameters[] = $id;

			$search_column = $column === NULL ? $this->_primary_key_field : $column;

			$sql .= 'WHERE `' . $search_column . '` = ? ';

			$where = FALSE;
		}

		$sql .= $this->_processWhere($where);

		$sql .= ' LIMIT 1';

		$result = DB::query(DB::SELECT, $sql, $this->_parameters);

		$this->_resetColumns();

		if(count($result) == 0)
		{
			$this->_setBlankProps();

			$this->_new_object = TRUE;

			return $this;
		}

		$result = $result[0];

		$this->_primary_key = $result[$this->_primary_key_field];
	
		$this->_setProps($result);

		$this->_new_object = FALSE;

		return $this;
  	}

  	public function find_all($id = NULL, $column = NULL)
	{
		if($this->loaded())
		{
			throw new Exception("Method find_all() cannot be called on a loaded object");
		}

		$search_column = $column === NULL ? $this->_primary_key_field : $column;

    	$sql = 'SELECT * FROM `' . $this->_table . '` ';

		$where = TRUE;

		if($column)
		{
			$sql .= 'WHERE `' . $search_column . '` = ? ';

			$where = FALSE;
		}

		if($id)
		{
			$this->_parameters[] = $id;
		}

		$sql .= $this->_processWhere($where);

		$sql .= $this->_processOrderBy();

		$result = DB::query(DB::SELECT, $sql, $this->_parameters);

		if(count($result) == 0)
		{
			return $this;
		}
		else
		{
			$return = array();

			$modelname = get_class($this);

			foreach($result as $row)
			{
				$return[$row[$this->_primary_key_field]] = new $modelname(NULL, $row, FALSE);
			}

			return $return;
		}
  	}

	public function where($condition, $column, $operator, $value)
	{
		$this->_where_clauses[] = array(
								'condition' => $condition,
								'column' => $column,
								'operator' => $operator,
								'value' => $value
							);

		return $this;
	}

	public function order_by($column, $direction = 'ASC')
	{
		$this->_order_by[] = array(
							'column' => $column,
							'direction' => $direction
		);

		return $this;
	}

	public function loaded()
	{
		return $this->_new_object === FALSE ? TRUE : FALSE;
	}

	public function save()
	{
		if($this->_new_object == FALSE && count($this->_changed) == 0)
		{
			return $this;
		}

		if($this->_new_object == FALSE && count($this->_changed) > 0)
		{
			$this->_update($this->_table, $this->_primary_key, $this->_primary_key_field, $this->_changed);
		}

		if($this->_new_object == TRUE && count($this->_changed) > 0)
		{
			$this->_create($this->_table, $this->_data);
		}

		return $this;
	}

  	public function delete()
	{
    	$sql = 'DELETE FROM `' . $this->_table . '` WHERE `' . $this->_primary_key_field . '` = ?';

    	$result = $result = DB::query(DB::DELETE, $sql, array($this->_primary_key));

		$this->_clearProps();

    	return $this;
  	}

	private function _processWhere($where = FALSE)
	{
		$string = '';

		if($where && count($this->_where_clauses) > 0)
		{
			$string .= 'WHERE ';
		}

		foreach($this->_where_clauses as $clause)
		{
			$string .= $clause['condition'] . ' ' . $clause['column'] . ' ' . $clause['operator'] . ' ? ';

			$this->_parameters[] = $clause['value'];
		}

		return $string;
	}

	private function _processOrderBy()
	{
		$string = '';

		if(count($this->_order_by) > 0)
		{
			$string .= ' ORDER BY ';

			for($i = 0;$i < count($this->_order_by);$i++)
			{
				$string .= $this->_order_by[$i]['column'] . ' ' . $this->_order_by[$i]['direction'];

				$string .= $i < count($this->_order_by) - 1 && count($this->_order_by) > 1 ? ', ' : '';
			}
		}

		return $string;
	}

	private function _create($table, $data = array())
	{
		$cols = array_keys($data);

		$vals = array_values($data);

		$sql = 'INSERT INTO ' . $table . ' (';

		for($i = 0;$i < count($cols);$i++)
		{
			$sql .= $i == count($cols) - 1 ? $cols[$i] . ') ' : $cols[$i] . ', ';
		}

		$sql .= 'VALUES (';

		for($i = 0;$i < count($vals);$i++)
		{
			$sql .= $i == count($vals) - 1 ? '?) ' : '?, ';
		}

		$result = DB::query(DB::INSERT, $sql, $vals);

		return $result;
	}

	private function _update($table, $id, $pk_field, $data)
	{
		$vals = array_values($data);

		array_push($vals, $id);

		$sql = 'UPDATE ' . $table . ' SET ';

		$i = 0;

		foreach($data as $column => $value)
		{
			$sql .= $i == count($data) - 1 ? $column . ' = ? ' : $column . ' = ?, ';

			$i++;
		}

		$sql .= 'WHERE ' . $pk_field . '= ?';

		$result = DB::query(DB::UPDATE, $sql, $vals);

		return $result;
	}

	private function _clearProps()
	{
		foreach($this->_data as $key => $val)
		{
			$this->_data[$key] = NULL;
		}
	}

	private function _setBlankProps()
	{
		$cols = DB::getColumnNames($this->_table);

		$this->_resetColumns();

		foreach($cols as $column_name)
		{
			$this->_columns[] = $column_name;

			$this->{$column_name} = NULL;
		}
	}

	private function _setProps($data)
	{
		foreach($data as $column_name => $val)
		{
			$this->_columns[] = $column_name;

			$this->{$column_name} = $val;
		}
	}

	private function _resetColumns()
	{
		$this->_columns = array();
	}
}
