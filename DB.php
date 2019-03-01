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

$db->insert('conta', array(
    'CONSUMO' => '10',
    'NOME' => 'test',
    'TOTALPAGAR' => '100',
    'TARIFA' => '10',
    'MULTA' => '22'
));

echo $db->getLastId();
