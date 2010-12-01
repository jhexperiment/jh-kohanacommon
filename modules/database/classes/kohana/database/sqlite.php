<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PDO database connection.
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Database_SQLite extends Database {

	// PDO uses no quoting for identifiers
	protected $_identifier = '';

	public function connect()
	{
		if ($this->_connection)
			return;

		// Extract the connection parameters, adding required variabels
		extract($this->_config['connection'] + array(
			'file'        => '',
		));

		// Clear the connection parameters for security
		unset($this->_config['connection']);

		if ( ! empty($persistent))
			{
			// Make the connection persistent
			$this->_connection = sqlite_popen($file, 0666, $sqliteerror);
			}
		else
			{
			// Create a new SQLite connection
			$this->_connection = sqlite_open($file, 0666, $sqliteerror);
			}
	}

	public function disconnect()
	{
		// Destroy the SQLite object
		sqlite_close($this->_connection);
		$this->_connection = NULL;

		return TRUE;
	}

	public function set_charset($charset)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		// Execute a raw SET NAMES query
		$this->_connection->exec('SET NAMES '.$this->quote($charset));
	}

	public function query($type, $sql, $as_object)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ( ! empty($this->_config['profiling']))
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		try
		{
			$result = sqlite_query($this->_connection, $sql);
		}
		catch (Exception $e)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			// Rethrow the exception
			throw $e;
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Database::SELECT)
		{
			// Convert the result into an array
			if ($as_object === FALSE)
			{
				$result = sqlite_fetch_all($result, SQLITE_ASSOC);
			}
			else
			{
				$record_list = array();
				while ($record = sqlite_fetch_object($result))
				{
					$record_list[] = $record;
				}
				$result = $record_list;
			}
			
			
			// Return an iterator of results
			return new Database_Result_Cached($result, $sql, $as_object);
		}
		elseif ($type === Database::INSERT)
		{
			// Return a list of insert id and rows created
			return array(
				sqlite_last_insert_rowid($this->_connection),
				sqlite_num_rows($result),
			);
		}
		else
		{
			// Return the number of rows affected
			return sqlite_num_rows($result);
		}
	}

	public function list_tables($like = NULL)
	{
		if (is_string($like))
		{
			// Search for table names
			$result = $this->query(Database::SELECT, 'SELECT name FROM SQLITE_MASTER WHERE type="table" AND name LIKE '.$this->quote($like).' ORDER BY name', FALSE);
		}
		else
		{
			// Find all table names
			$result = $this->query(Database::SELECT, 'SELECT name FROM SQLITE_MASTER WHERE type="table" ORDER BY name', FALSE);
		}

		return $result;
	}

	public function list_columns($table, $like = NULL)
	{
		if (is_string($like))
		{
			throw new Kohana_Exception('Database method :method is not supported by :class',
				array(':method' => __FUNCTION__, ':class' => __CLASS__));
		}

		// Find all column names
		$result = $this->query(Database::SELECT, 'PRAGMA table_info('.$table.')', FALSE);

		$columns = array();
		foreach ($result as $row)
		{
			// Get the column name from the results
			$columns[] = $row['name'];
		}

		return $columns;
	}

	public function escape($value)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return sqlite_escape_string($value);
	}

} // End Database_SQLite
