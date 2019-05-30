<?php
/**
 * User: Arthur
 * Date: 15/02/2019
 * Time: 01:08
 */

class DB
{

    private $db;
    private $result;
    private $dsn = array(
        'dbname' => '', // NAME DATABASE
        'host' => 'localhost', // DEFAULT
        'dbdriver' => 'mysql'
    );
    private $login = "root";
    private $password = "root";
    private $lastId;

    public function __construct()
    {

        try {
            $this->db = new PDO($this->getDsn(), $this->login, $this->password);
        } catch (PDOException $error) {
            exit("Error CODE: {$error->getCode()} </br> ERROR Mensage: {$error->getMessage()}");
        }


    }

    private function getDsn()
    {
        return "{$this->dsn['dbdriver']}:host={$this->dsn['host']};dbname={$this->dsn['dbname']}";
    }

    public function getConection()
    {
        return $this->db;
    }

    public function get($table)
    {
        $query = $this->getConection()->prepare("SELECT * FROM $table");
        $query->execute();
        $this->setResult($query);
        return $this;
    }

    public function get_where($table, $conditions = array())
    {
        $query = "SELECT * FROM $table WHERE ";
        $prepare = array();
        $where = array();

        if (is_array($conditions)) {
            foreach ($conditions as $key => $value) {
                $where[] = $key . "=" . ":" . $key;
                $prepare[$key] = $value;
            }

            $query = $query . implode(' AND ', $where);

            echo $query;
            $result = $this->getConection()->prepare($query);

            foreach ($prepare as $item => $value) {
                $result->bindValue(":{$item}", $value);
            }

            if ($result->execute()) {
                $this->setResult($result);
            }
        }


        return $this;

    }

    public function update($table, $values = array(), $conditions = array())
    {
        $query = "UPDATE $table SET ";
        $prepare = array();
        $bind = array();
        $where = array();
        $prepareWhere = array();

        foreach ($values as $key => $val) {
            $prepare[] = $key . "=" . ":" . $key;
            $bind[$key] = $val;
        }
        $query = $query . implode(', ', $prepare);

        foreach ($conditions as $key => $val) {
            $prepareWhere[] = $key . "=" . ":" . $key;
            $where[$key] = $val;
        }

        $query = $query . " WHERE " . implode("WHERE", $prepareWhere);

        $query = $this->getConection()->prepare($query);

        $prepare = array_merge($prepare, $prepareWhere);

        $bind = array_merge($bind, $where);

        foreach ($prepare as $keyPrepare => $val) {
            echo ":{$keyPrepare}". $val;
           $query->bindValue(":{$keyPrepare}", $val);
        }

        foreach ($bind as $keyBind => $val) {
            $query->bindValue(":{$keyBind}", $val);
        }

    }

    public function setResult($query)
    {
        $this->result = $query;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function result_array()
    {
        return $this->result->fetchAll();
    }

    public function row_array()
    {
        return $this->result->fetch();
    }

    public function insert($table, $values = array())
    {
        $query = array();
        $prepare = array();
        $insert = "INSERT INTO $table SET ";

        foreach ($values as $key => $val) {
            $prepare[] = "{$key} = :{$key}";
            $query[$key] = $val;
        }

        $insert = $insert . implode(', ', $prepare);

        $bind = $this->getConection()->prepare($insert);

        foreach ($query as $item => $value) {
            $bind->bindValue(":{$item}", $value);
        }

        if ($bind->execute()) {
            $this->setResult($bind);
            $this->setLastId($this->getConection()->lastInsertId());
            return true;
        }

        return false;
    }

    public function setLastId($id)
    {
        $this->lastId = $id;
    }

    public function getLastId()
    {
        return $this->lastId;
    }

    public function num_rows()
    {
        return $this->getResult()->rowCount();
    }


}

$db = new DB();

$db->update("conta", array('CONSUMO' => "20", 'CONSUMO' => "20", 'teste' => "20", 'nome' => "20"), array("ID" => "846"));