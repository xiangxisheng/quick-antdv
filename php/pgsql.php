<?php

class PgSQL
{
    private $db;
    public function __construct($dsn, $user, $pass)
    {
        $this->db = new PDO($dsn, $user, $pass);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function fetchAll($sql, $param)
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($param);
        $this->db->rollBack();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
