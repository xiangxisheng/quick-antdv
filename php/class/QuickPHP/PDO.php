<?php

namespace QuickPHP;

class PDO extends \PDO
{

    public function conn($dsn, $user, $pass)
    {
        parent::__construct($dsn, $user, $pass);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function begin()
    {
        $this->beginTransaction();
    }

    public function execute($sql, $param)
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($param);
        return $stmt;
    }

    public function fetchAll($sql, $param)
    {
        $this->begin();
        $stmt = $this->execute($sql, $param);
        $this->rollBack();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetch($sql, $param)
    {
        $this->begin();
        $stmt = $this->execute($sql, $param);
        $this->rollBack();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchNum($sql, $param)
    {
        $this->begin();
        $stmt = $this->execute($sql, $param);
        $this->rollBack();
        return $stmt->fetch(PDO::FETCH_NUM);
    }
}
