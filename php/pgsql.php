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

    private function getColumnByField($aColumns, $sField)
    {
        foreach ($aColumns as $column) {
            if ($column['dataIndex'] === $sField) {
                return $column;
            }
        }
    }

    private function getOrderSql(&$aSql, $data)
    {
        $mSorter = $data['sql']['sorter'];
        if (!$mSorter) {
            return;
        }
        if (!$mSorter['field']) {
            return;
        }
        $mColumn = $this->getColumnByField($data['columns'], $mSorter['field']);
        if (!$mColumn) {
            return;
        }
        if (!$mColumn['sorter']) {
            return;
        }
        $sField = $mColumn['dataIndex'];
        $mOrderDict = array(
            'ascend' => 'ASC',
            'descend' => 'DESC',
        );
        if ($mOrderDict[$mSorter['order']]) {
            $sOrder = $mOrderDict[$mSorter['order']];
            return "ORDER BY {$sField} {$sOrder}";
        }
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
        $sSqlOrder = $this->getOrderSql($aSql, $data);
        if ($sSqlOrder) {
            $aSql[] = $sSqlOrder;
        } else if ($data['sql']['order']) {
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

    private function getParamFromSqlAndValue($sql_where, $aValue)
    {
        if (strstr($sql_where, 'LIKE')) {
            return '%' . $aValue[0] . '%';
        }
        return $aValue[0];
    }

    private function putWhereAndParam($column, &$aWhere, &$aParam, $aValue)
    {
        $aWhere[] = $column['sql_where'];
        $aParam[] = $this->getParamFromSqlAndValue($column['sql_where'], $aValue);
    }

    public function tableReader($data)
    {
        $data['sql']['param'] = array();
        $filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : array();
        foreach ($data['columns'] as $column) {
            if (!isset($column['sql_where'])) {
                continue;
            }
            if (isset($filters[$column['dataIndex']])) {
                $this->putWhereAndParam($column, $data['sql']['where'], $data['sql']['param'], $filters[$column['dataIndex']]);
            }
        }
        $data['sql']['sorter'] = isset($_GET['sorter']) ? json_decode($_GET['sorter'], true) : array();
        $pagination = isset($_GET['pagination']) ? json_decode($_GET['pagination'], true) : array();
        $pageSize = isset($pagination['pageSize']) ? $pagination['pageSize'] : 20;
        if ($pageSize < 1) {
            $pageSize = 1;
        }
        $pageMax = isset($data['info']['pageMax']) ? $data['info']['pageMax'] : 100;
        if ($pageSize > $pageMax) {
            $pageSize = $pageMax;
        }
        $total = $this->getRecordTotal($data);
        $pageCount = ceil($total / $pageSize);
        $current = isset($pagination['current']) ? $pagination['current'] : 1;
        if ($current > $pageCount) {
            // 页码不能超过最大页数
            $current = $pageCount;
        }
        if ($current < 1) {
            $current = 1;
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
