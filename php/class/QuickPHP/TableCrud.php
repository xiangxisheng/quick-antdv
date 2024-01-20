<?php

namespace QuickPHP;

class TableCrud extends PDO
{

    public function __construct($dbConf)
    {
        $this->conn($dbConf['dsn'], $dbConf['user'], $dbConf['pass']);
    }

    private function getColumnByField($aColumns, $sField)
    {
        foreach ($aColumns as $column) {
            if (!isset($column['dataIndex'])) {
                continue;
            }
            if ($column['dataIndex'] === $sField) {
                return $column;
            }
        }
    }

    private function getOrderSql($data)
    {
        $mSorter = $data['sql']['sorter'];
        if (!$mSorter) {
            return;
        }
        if (!$mSorter['field']) {
            return;
        }
        $mColumn = $this->getColumnByField($data['table']['columns'], $mSorter['field']);
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
        $sSorterOrder = isset($mSorter['order']) ? $mSorter['order'] : '';
        $sOrder = isset($mOrderDict[$sSorterOrder]) ? $mOrderDict[$sSorterOrder] : '';
        if ($sOrder) {
            $sOrder = $mOrderDict[$mSorter['order']];
            return "ORDER BY {$sField} {$sOrder}";
        }
    }

    private function fetchOne($data, $value)
    {
        $aSelect = array();
        foreach ($data['table']['columns'] as $column) {
            if (!isset($column['dataIndex'])) {
                continue;
            }
            $aSelect[] = (isset($column['sql_selone']) ? $column['sql_selone'] . ' ' : '') . $column['dataIndex'];
        }
        $aSql = array();
        $aSql[] = 'SELECT ' . implode(',', $aSelect);
        $aSql[] = 'FROM ' . $data['sql']['from'];
        $aSql[] = 'WHERE ' . $data['table']['rowKey'] . '=?';
        $sSql = implode("\r\n", $aSql);
        $data['sql']['param'][] = $value;
        return $this->fetch($sSql, $data['sql']['param']);
    }

    private function fetchAllSelect($data)
    {
        $aSelect = array();
        foreach ($data['table']['columns'] as $column) {
            if (!isset($column['dataIndex'])) {
                continue;
            }
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
        $sSqlOrder = $this->getOrderSql($data);
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


    private function table_action_list($data)
    {
        $data['sql']['param'] = array();
        $filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : array();
        foreach ($data['table']['columns'] as $column) {
            if (!isset($column['dataIndex'])) {
                continue;
            }
            if (!isset($column['sql_where'])) {
                continue;
            }
            if (isset($filters[$column['dataIndex']])) {
                $this->putWhereAndParam($column, $data['sql']['where'], $data['sql']['param'], $filters[$column['dataIndex']]);
            }
        }
        $data['sql']['sorter'] = isset($_GET['sorter']) ? json_decode($_GET['sorter'], true) : array();
        $pagination = isset($_GET['pagination']) ? json_decode($_GET['pagination'], true) : array();
        $pageSizeDefault = isset($data['table']['pagination']['pageSizeDefault']) ? $data['table']['pagination']['pageSizeDefault'] : 20;
        $pageSize = isset($pagination['pageSize']) ? $pagination['pageSize'] : $pageSizeDefault;
        if ($pageSize < 1) {
            $pageSize = 1;
        }
        $pageMax = isset($data['table']['pagination']['pageSizeMax']) ? $data['table']['pagination']['pageSizeMax'] : 100;
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
        $data['sql']['limit'] = $pageSize;
        $data['sql']['offset'] = ($current - 1) * $pageSize;
        $dataSource = $this->fetchAllSelect($data);

        foreach ($data['table']['columns'] as $column) {
            if (!isset($column['dataIndex'])) {
                continue;
            }
            if (isset($column['valueFunc'])) {
                foreach ($dataSource as &$row) {
                    // 对数据进行二次处理
                    $row[$column['dataIndex']] = $column['valueFunc']($row[$column['dataIndex']]);
                }
            }
        }

        return [
            'pagination' => [
                'total' => $total,
                'current' => $current,
                'pageSize' => $pageSize,
            ],
            'dataSource' => $dataSource,
        ];
    }

    public function tableReader($data)
    {
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if ($action === 'init') {
            $data['table'] = array_merge_recursive($data['table'], $this->table_action_list($data));
            unset($data['sql']);
            foreach ($data['table']['columns'] as &$column) {
                unset($column['sql_select']);
            }
            if (isset($data['table']['pagination'])) {
                unset($data['table']['pagination']['pageSizeDefault']);
                unset($data['table']['pagination']['pageSizeMax']);
            }
            return $data;
        }

        if (in_array($action, ['view', 'edit'])) {
            if (!isset($_GET['id'])) {
                return;
            }
            $id = $_GET['id'];
            $formModel = $this->fetchOne($data, $id);
            return [
                'formModel' => $formModel,
            ];
        }

        if ($action === 'list') {
            return [
                'table' => $this->table_action_list($data),
            ];
        }
    }
}
