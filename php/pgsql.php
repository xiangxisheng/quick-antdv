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

    public function fetchNum($sql, $param)
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($param);
        $this->db->rollBack();
        return $stmt->fetch(PDO::FETCH_NUM);
    }

    private function fetchAllSelect($data)
    {
        $aSelect = array();
        foreach ($data['columns'] as $column) {
            $aSelect[] = (isset($column['sql_select']) ? $column['sql_select'] . ' ' : '') . $column['dataIndex'];
        }
        $aSql = array();
        $aSql[] = 'SELECT ' . implode(',', $aSelect);
        $aSql[] = 'FROM ' . $data['sql']['from'];
        if (count($data['sql']['where']) > 0) {
            $aSql[] = 'WHERE ' . '(' . implode(')AND(',  $data['sql']['where']) . ')';
        }
        if (isset($data['sql']['group'])) {
            $aSql[] = 'GROUP BY ' . $data['sql']['group'];
        }
        if ($data['sql']['order']) {
            $aSql[] = 'ORDER BY ' . $data['sql']['order'];
        }
        if ($data['sql']['limit']) {
            $aSql[] = 'LIMIT ' . $data['sql']['limit'];
        }
        if ($data['sql']['offset']) {
            $aSql[] = 'OFFSET ' . $data['sql']['offset'];
        }
        $sSql = implode("\r\n", $aSql);
        return $this->fetchAll($sSql, $data['sql']['param']);
    }

    private function getRecordTotal($data)
    {
        $aSql = array();
        $aSql[] = 'SELECT COUNT(*)';
        $aSql[] = 'FROM ' . $data['sql']['from'];
        if (count($data['sql']['where']) > 0) {
            $aSql[] = 'WHERE ' . '(' . implode(')AND(',  $data['sql']['where']) . ')';
        }
        if (isset($data['sql']['group'])) {
            $aSql[] = 'GROUP BY ' . $data['sql']['group'];
        }
        $sSql = implode("\r\n", $aSql);
        if (isset($data['sql']['group'])) {
            $sSql = "SELECT COUNT(*) FROM ({$sSql}) table_count";
        }
        return $this->fetchNum($sSql, $data['sql']['param'])[0];
    }

    public function tableReader($data)
    {
        $data['sql']['param'] = array();
        $filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : array();
        foreach ($data['columns'] as $column) {
            if (isset($filters[$column['dataIndex']])) {
                $data['sql']['where'][] = $column['sql_where'];
                $data['sql']['param'][] = $filters[$column['dataIndex']][0];
            }
        }
        $pagination = isset($_GET['pagination']) ? json_decode($_GET['pagination'], true) : array();
        $current = isset($pagination['current']) ? $pagination['current'] : 1;
        if ($current < 1) {
            $current = 1;
        }
        $pageSize = isset($pagination['pageSize']) ? $pagination['pageSize'] : 20;
        if ($pageSize < 1) {
            $pageSize = 1;
        }
        $total = $this->getRecordTotal($data);
        $pages = ceil($total / $pageSize);
        if ($current > $pages) {
            // 页码不能超过最大页数
            $current = $pages;
        }
        $pageSizeMax = isset($data['info']['pageSizeMax']) ? $data['info']['pageSizeMax'] : 100;
        if ($pageSize > $pageSizeMax) {
            $pageSize = $pageSizeMax;
        }
        $data['pagination'] = array();
        $data['pagination']['total'] = $total;
        $data['pagination']['current'] = $current;
        $data['pagination']['pageSize'] = $pageSize;
        $data['sql']['limit'] = $pageSize;
        $data['sql']['offset'] = ($current - 1) * $pageSize;
        $data['rows'] = $this->fetchAllSelect($data);
        unset($data['sql']);
        foreach ($data['columns'] as &$column) {
            unset($column['sql_select']);
        }
        return $data;
    }
}
