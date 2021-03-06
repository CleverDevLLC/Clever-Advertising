<?php

namespace core\engine;

class DB
{
    const PATH_TO_MYSQLIERRORS = PATH_TO_TMP_DIR . '/mysqli-errors.log';
    const ALL_COLLUMNS = '*';

    private function __construct()
    {
    }

    static private $_instance;

    private $_connection;

    private static function inst()
    {
        if (is_null(self::$_instance)) {
            self::initializeErrorFile();
            self::$_instance = new self();
            self::$_instance->connect();
        }
        return self::$_instance;
    }

    private function connect()
    {
        function connectionError($code)
        {
            die("DB CONNECTION ERROR $code");
        }

        $this->_connection = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)
        or connectionError(1);
        $this->_connection->select_db(DB_NAME)
        or connectionError(2);
        $this->_connection->set_charset('utf8');
        $this->query("SET SESSION group_concat_max_len = 1000000;");
    }

    static public function query($query)
    {
        $db = self::inst();
        $result = $db->_connection->real_query($query);
        if (!$result) {
            $db->error($query . PHP_EOL . $db->_connection->error);
            return NULL;
        }
        return $result;
    }

    static private function mysqlnd_get_result($statement)
    {
        $result = array();
        $statement->store_result();
        for ($i = 0; $i < $statement->num_rows; $i++) {
            $metadata = $statement->result_metadata();
            $PARAMS = array();
            while ($Field = $metadata->fetch_field()) {
                $PARAMS[] = &$result[$i][$Field->name];
            }
            call_user_func_array(array($statement, 'bind_result'), $PARAMS);
            $statement->fetch();
        }
        return $result;
    }

    static private function prepareStatementExecute($query, $values = [])
    {
        $db = self::inst();
        $stmt = $db->_connection->prepare($query);
        if (!$stmt) {
            self::error("no stmt for query $query: {$db->_connection->error}");
            return [];
        }

        $types = '';
        foreach ($values as $value) {
            if (is_bool($value)) {
                $types .= 'i';
            } elseif (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        if (count($values)) {
            mysqli_stmt_bind_param($stmt, $types, ...$values);
        }
        if (!$stmt->execute()) {
            self::error("cant execute query $query: {$stmt->error}.\n" .
                json_encode($values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        $result = self::mysqlnd_get_result($stmt);
        return $result;
    }

    static public function get($query, $values = [])
    {
        return self::prepareStatementExecute($query, $values);
    }

    static public function set($query, $values = [])
    {
        return self::prepareStatementExecute($query, $values);
    }

    static public function lastInsertId()
    {
        return DB::inst()->_connection->insert_id;
    }

    static private function initializeErrorFile()
    {
        if (!is_file(self::PATH_TO_MYSQLIERRORS)) {
            self::error('error file initializing');
            chmod(self::PATH_TO_MYSQLIERRORS, 0777);
        }
    }

    static private function error($text)
    {
        $datetime = self::timetostr(time());
        file_put_contents(self::PATH_TO_MYSQLIERRORS, "\r\n$datetime\r\n$text\r\n", FILE_APPEND);
        return $text;
    }

    static public function timetostr($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
}