<?php
namespace Craft;

/**
 *
 */
class DbBackup
{
	private $_constraints;
	private $_currentVersion;

	/**
	 * Dump all tables
	 *
	 * @return string
	 */
	public function run()
	{
		$this->_currentVersion = 'v'.Craft::getStoredVersion().'.'.Craft::getStoredBuild();
		$result = $this->_processHeader();

		foreach (craft()->db->getSchema()->getTables() as $tableName => $val)
		{
			$result .= $this->_processTable($tableName);
		}

		$result .= $this->_processConstraints();
		$result .= $this->_processFooter();

		$fileName = gmdate('ymd_His').'_'.$this->_currentVersion.'.sql';
		$filePath = craft()->path->getDbBackupPath().strtolower($fileName);
		IOHelper::writeToFile($filePath, $result);

		return $filePath;
	}

	/**
	 * @param $filePath
	 *
	 * @throws Exception
	 */
	public function restore($filePath)
	{
		if (!IOHelper::fileExists($filePath))
		{
			throw new Exception(Craft::t('Could not find the SQL file to restore: {filePath}', array('filePath' => $filePath)));
		}

		$this->_nukeDb();

		$sql = IOHelper::getFileContents($filePath);

		Craft::log('Executing SQL statement: '.$sql);
		$command = craft()->db->createCommand($sql);
		$command->execute();
	}

	/**
	 *
	 */
	private function _nukeDb()
	{
		Craft::log('Nuking DB');

		$databaseName = craft()->config->getDbItem('database');

		$sql = 'SET FOREIGN_KEY_CHECKS = 0;'.PHP_EOL.PHP_EOL;

		$tables = craft()->db->getSchema()->getTableNames();
		foreach ($tables as $table)
		{
			$sql .= 'DROP TABLE IF EXISTS '.craft()->db->quoteDatabaseName($databaseName).'.'.craft()->db->quoteTableName($table).';'.PHP_EOL;
		}

		$sql .= PHP_EOL.'SET FOREIGN_KEY_CHECKS = 1;'.PHP_EOL;

		$command = craft()->db->createCommand($sql);
		$command->execute();

		Craft::log('Database nuked.');
	}


	/**
	 * Generate the foreign key constraints for all tables
	 *
	 * @return string
	 */
	private function _processConstraints()
	{
		$sql = '--'.PHP_EOL.'-- Constraints for tables'.PHP_EOL.'--'.PHP_EOL.PHP_EOL;
		$first = true;

		foreach ($this->_constraints as $tableName => $value)
		{
			if ($first && count($value[0]) > 0)
			{
				$sql .= PHP_EOL.'--'.PHP_EOL.'-- Constraints for table '.craft()->db->quoteTableName($tableName).PHP_EOL.'--'.PHP_EOL;
				$sql .= 'ALTER TABLE '.craft()->db->quoteTableName($tableName).PHP_EOL;
			}

			if (count($value[0]) > 0)
			{
				for ($i = 0; $i < count($value[0]); $i++)
				{
					if (strpos($value[0][$i], 'CONSTRAINT') === false)
					{
						$sql .= preg_replace('/(FOREIGN[\s]+KEY)/', "\tADD $1", $value[0][$i]);
					}
					else
					{
						$sql .= preg_replace('/(CONSTRAINT)/', "\tADD $1", $value[0][$i]);
					}

					if ($i == count($value[0]) - 1)
					{
						$sql .= ";".PHP_EOL;
					}
					if ($i < count($value[0]) - 1)
					{
						$sql .=PHP_EOL;
					}
				}
			}
		}

		return $sql;
	}


	/**
	 * Set sql file header
	 * @return string
	 */
	private function _processHeader()
	{
		$header = '-- Generated by CraftCMS '.$this->_currentVersion.' on '.DateTimeHelper::nice(DateTimeHelper::currentTimeStamp()).'.'.PHP_EOL.PHP_EOL;
		$header .= '--'.PHP_EOL.'-- Disable foreign key checks and autocommit and start a transaction.'.PHP_EOL.'--'.PHP_EOL.PHP_EOL;
		$header .= 'SET FOREIGN_KEY_CHECKS = 0;'.PHP_EOL;
		$header .= 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";'.PHP_EOL;
		$header .= 'SET AUTOCOMMIT = 0;'.PHP_EOL;
		$header .= 'START TRANSACTION;'.PHP_EOL.PHP_EOL;
		$header .= '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;'.PHP_EOL;
		$header .= '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;'.PHP_EOL;
		$header .= '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;'.PHP_EOL;
		$header .= '/*!40101 SET NAMES utf8 */;'.PHP_EOL;

		return $header;
	}


	/**j
	 * Set sql file footer
	 * @return string
	 */
	private function _processFooter()
	{
		$footer = PHP_EOL.'SET FOREIGN_KEY_CHECKS = 1;'.PHP_EOL;
		$footer .= 'COMMIT;'.PHP_EOL.PHP_EOL;
		$footer .= '/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;'.PHP_EOL;
		$footer .= '/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;'.PHP_EOL;
		$footer .= '/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;'.PHP_EOL;

		return $footer;
	}


	/**
	 * Create the SQL for a table dump
	 *
	 * @param $tableName
	 * @return mixed
	 */
	private function _processTable($tableName)
	{
		$db = craft()->db;

		$result = PHP_EOL.'--'.PHP_EOL.'-- Schema for table `'.$tableName.'`'.PHP_EOL.'--'.PHP_EOL;
		$result .= PHP_EOL.'DROP TABLE IF EXISTS '.$db->quoteTableName($tableName).';'.PHP_EOL.PHP_EOL;

		$q = $db->createCommand('SHOW CREATE TABLE '.$db->quoteTableName($tableName).';')->queryRow();
		$createQuery = $q['Create Table'];
		$pattern = '/CONSTRAINT.*|FOREIGN[\s]+KEY/';

		// constraints to $tableName
		preg_match_all($pattern, $createQuery, $this->_constraints[$tableName]);

		$createQuery = preg_split('/$\R?^/m', $createQuery);
		$createQuery = preg_replace($pattern, '', $createQuery);

		$removed = false;
		foreach ($createQuery as $key => $statement)
		{
			// Stupid PHP.
			$temp = trim($createQuery[$key]);
			if (empty($temp))
			{
				unset($createQuery[$key]);
				$removed = true;
			}
		}

		if ($removed)
		{
			$createQuery[count($createQuery) - 2] = rtrim($createQuery[count($createQuery) - 2], ',');
		}

		// resort the keys
		$createQuery = array_values($createQuery);

		for ($i = 0; $i < count($createQuery) - 1; $i++)
		{
				$result .= $createQuery[$i].PHP_EOL;
		}

		$result .= $createQuery[$i].';'.PHP_EOL;
		$rows = $db->createCommand('SELECT * FROM '.$db->quoteTableName($tableName).';')->queryAll();

		if (empty($rows))
		{
			return $result;
		}

		$result .= PHP_EOL.'--'.PHP_EOL.'-- Data for table `'.$tableName.'`'.PHP_EOL.'--'.PHP_EOL.PHP_EOL;
		$attrs = array_map(array($db, 'quoteColumnName'), array_keys($rows[0]));

		$result .= 'INSERT INTO '.$db->quoteTableName($tableName).' ('.implode(', ', $attrs).') VALUES'.PHP_EOL;

		$i = 0;
		$rowsCount = count($rows);

		foreach($rows as $row)
		{
			// Process row
			foreach($row as $columnName => $value)
			{
				if ($value === null)
				{
					$row[$columnName] = 'NULL';
				}
				else
				{
					$row[$columnName] = $db->getPdoInstance()->quote($value);
				}
			}

			$result .= ' ('.implode(', ', $row).')';

			if ($i < $rowsCount - 1)
			{
				$result .= ',';
			}
			else
			{
				$result .= ';';
			}

			$result .= PHP_EOL;
			$i++;
		}

		$result .= PHP_EOL.PHP_EOL;

		return $result;
	}
}
