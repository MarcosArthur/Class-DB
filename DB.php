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
        'dbname' => 'contas',
        'host' => 'localhost', // DEFAULT
        'dbdriver' => 'mysql'
    );
    private $login = "root";
    private $password = "root";
    private $where = array();

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
        $query = "SELECT * FROM $table";

        if (!empty($conditions) && count($conditions) && is_array($conditions)) {

            foreach ($conditions as $key => $value) {
                $this->where[] = $key . "=" . $value;
            }

//            $query += $where;
            var_dump($this->where);

        }


    }

    public function setResult($query)
    {
        $this->result = $query;
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
            return true;
        }

        return false;


    }


}

