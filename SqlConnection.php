<?php 
namespace DB_Class;

/**
 * @version 1.0.7
 * @author Erik Carrillo
 */

class SqlConnection
{
	public $Con;
	private $Query;
	public $Reg;

	private $MDB;
	private $DBName;

	public $ErrorDB;
	public $NumRows=0;
	
	function __construct($var1='MySQL',$var2='')
	{
		$this->MDB = $var1;
		$this->DBName = $var2;
		$this->Inicializar();
	}

	private function Inicializar()
	{
		switch ($this->MDB) 
		{
			case 'MySQL':
				//@mysqli_connect(servidor, usuario, contraseña, nombre_bd , puerto)
				$this->Con = @mysqli_connect('localhost', 'root', '', $this->DBName, 3306);
				break;
			case 'PgSQL':
				$this->DBName = $this->DBName!=""? " dbname=" . $this->DBName: null;
				$this->Con = @pg_connect("host=localhost user=postgres password=test ".$this->DBName); 
				break;
			case 'ODBC':
				//odbc_connect(dsn,user,pass);
				$this->Con = @odbc_connect("DRIVER={Microsoft Access Driver (*.mdb)};DBQ={$this->DBName};",'','');
				break;
			case 'MSSQL':
				$this->Con = @odbc_connect("Driver={SQL Server}; Server=127.0.0.1; charset=UTF-8;",
					'uruario',
					'contraseña');
				break;
			default:break;
		}
	}

	public function Exec($sql)
	{
		if(!$this->Con){ return; }
		switch ($this->MDB) 
		{
			case 'MySQL':
				$this->Query = @mysqli_query($this->Con, $sql);
				$this->ErrorDB = @mysqli_error($this->Con);
				$this->NumRows = @mysqli_num_rows($this->Query);
				break;
			case 'PgSQL':
				$this->Query = @pg_query($this->Con, $sql);
				$this->ErrorDB = @pg_last_error($this->Con);
				$this->NumRows = @pg_num_rows($this->Query);
				break;
			case 'ODBC':
			case 'MSSQL':
				$this->Query = @odbc_exec($this->Con, $sql);
				$this->ErrorDB = @odbc_errormsg($this->Con);
				$this->NumRows = @odbc_num_rows($this->Query);
				break;
			default:break;
		}

		if($this->Query){return true;}
		else{return false;}
	}

	public function Fetch()
	{
		switch ($this->MDB) 
		{
			case 'MySQL':
				$this->Reg = @mysqli_fetch_array($this->Query);
				break;
			case 'PgSQL':
				$this->Reg = @pg_fetch_array($this->Query, NULL, PGSQL_BOTH);
				break;
			case 'ODBC':
			case 'MSSQL':
				$this->Reg = @odbc_fetch_array($this->Query);
				break;
			default:break;
		}

		if($this->Reg){return true;}
		else{return false;}
	}
}
