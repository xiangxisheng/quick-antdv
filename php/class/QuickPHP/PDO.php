<?php

namespace QuickPHP;

class PDO extends \PDO
{
    private $db;

    public function conn($dsn, $user, $pass)
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

    public function fetch($sql, $param)
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($param);
        $this->db->rollBack();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchNum($sql, $param)
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($param);
        $this->db->rollBack();
        return $stmt->fetch(PDO::FETCH_NUM);
    }
}
