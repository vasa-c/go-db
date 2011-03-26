<?php
/**
 *	Надстройка над php_pgsql
 *
 * @package  go\DB
 * @subpackage Implementations
 * @author  Alex Polev
 */

namespace go\DB\Implementations;

final class pgsql extends Base
{
	/**
     * Обязательные параметры подключения
     *
     * @var array
     */
    protected $paramsReq = array();

	/**
     * Необязательные параметры подключения
     *
     * параметр => значение по умолчанию
     *
     * @var array
     */
    protected $paramsDefault = array(
        'dbname'          => null,
        'charset'         => null,
		'port'            => null,
		'hostaddr'        => null,
		'connect_timeout' => null,
		'options'         => null,
		'sslmode'         => null,
		'service'         => null,
    );


    /**
     * @override Base
     *
     * @param array $params
     * @param string & $errroInfo
     * @param int & $errorCode
     * @return mixed
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null) {
		if(isset ($params['host'])){
			$host = \explode(':', $params['host'], 2);
			if (!empty($host[1])) {
				$params['host'] =  $host[0];
				$params['port'] =  $host[1];
			}
		}

        $connection = @\pg_connect($this->generateConnectString($params));

		if(!$connection){
			$this->errorInfo = \error_get_last();
			return false;
		}

        return $connection;
    }


    /**
     * @override Base
     *
     * @param mixed $connection
     */
    public function close($connection) {
        return @\pg_close($connection);
    }

	/**
     * @override Base
     *
     * @param mixed $connection
     * @param string $query
     * @return mixed
     */
    public function query($connection, $query) {
        return \pg_query($connection, $query);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return mixed
     */
    public function getInsertId($connection, $cursor = null) {
		$result  =  @\pg_query($connection, 'SELECT lastval()');

		if(!$result){
			return  false;
		}
		
		$row  =  pg_fetch_row($result);
		
		return  $row[0];
    }

	/**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
	 */
	public  function  getAffectedRows($connection, $cursor = null) {
		return \pg_affected_rows($cursor);
	}

	/**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return string
     */
    public function getErrorInfo($connection, $cursor = null) {
        return \pg_errormessage($connection);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getErrorCode($connection, $cursor = null) {
        return  null;
    }

	/**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return int
     */
    public function getNumRows($connection, $cursor) {
        return \pg_numrows($cursor);
    }

	/**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchRow($connection, $cursor) {
        return \pg_fetch_row($cursor);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchAssoc($connection, $cursor) {
        return \pg_fetch_assoc($cursor);
    }

	/**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($connection, $cursor) {
        return \pg_fetch_object($cursor);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function freeCursor($connection, $cursor) {
        return \pg_free_result($cursor);
    }

	/**
     * @override Base
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function escapeString($connection, $value) {
        return \pg_escape_string($connection, $value);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprString($connection, $value) {
        return '\''.$this->escapeString($connection, $value).'\'';
    }
	
    /**
     * @override Base
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    protected function reprField($connection, $value) {
        return '"'.$value.'"';
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function rewindCursor($connection, $cursor) {
        return \pg_result_seek($cursor, $offset);
    }
	
    /**
     * Генерируем строку для подключения к БД
     *
     * @param array  $params параметры для подключения (@see $this->paramsDefault)
     *
     * @return  String
     */
    private  function  generateConnectString($params){

        $connString  =  '';
        if($params){
            foreach($params  as  $key=>$value){

                if(!$value){
                    continue;
                }

                switch($key){
                    case 'username':
                        $connString  .=  'user='.$value;
                    break;

					case 'charset':
						$connString  .=  'options=\'--client_encoding='.$value.'\'';
					break;
                    default:
                        $connString  .=  $key.'='.$value;
                    break;
                }

                $connString  .=  ' ';
            }
        }

        return  rtrim($connString);
    }
}
