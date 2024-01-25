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
			$aSelect[] = (isset($column['sql_selone']) ? $column['sql_selone'] . ' ' : '') . $this->fieldQuote($column['dataIndex']);
		}
		$aSql = array();
		$aSql[] = 'SELECT ' . implode(',', $aSelect);
		$aSql[] = 'FROM ' . $data['sql']['from'];
		$aSql[] = 'WHERE ' . $this->fieldQuote($data['table']['rowKey']) . '=?';
		$sSql = implode("\r\n", $aSql);
		$data['sql']['param'][] = $value;
		return $this->fetch($sSql, $data['sql']['param']);
	}

	private function buildSqlCreate($pageConfig, $mParam)
	{
		$aParam = [];
		$aSql = [];
		$aSql[] = 'INSERT INTO ' . $pageConfig['sql']['from'];
		$aFields = [];
		$aValues = [];
		foreach ($pageConfig['table']['columns'] as $column) {
			if (!isset($column['form'])) {
				continue;
			}
			if (isset($column['disabled']) && $column['disabled']) {
				continue;
			}
			if (isset($column['readonly']) && $column['readonly']) {
				continue;
			}
			if (array_key_exists($column['dataIndex'], $mParam)) {
				$aFields[] = $this->fieldQuote($column['dataIndex']);
				$aValues[] = '?';
				$aParam[] = $mParam[$column['dataIndex']];
			}
		}
		$aSql[] = '(' . implode(',', $aFields) . ')VALUES(' . implode(',', $aValues) . ')';
		$sSql = implode("\r\n", $aSql);
		return array($sSql, $aParam);
	}

	private function buildSqlSelect($data)
	{
		$aSelect = array();
		foreach ($data['table']['columns'] as $column) {
			if (!isset($column['dataIndex'])) {
				continue;
			}
			$aSelect[] = (isset($column['sql_select']) ? $column['sql_select'] . ' ' : '') . $this->fieldQuote($column['dataIndex']);
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
		return implode("\r\n", $aSql);
	}

	private function buildSqlUpdate($pageConfig, $mParam, $id)
	{
		$aParam = [];
		$aSql = [];
		$aSql[] = 'UPDATE ' . $pageConfig['sql']['from'];
		$aSets = [];
		foreach ($pageConfig['table']['columns'] as $column) {
			if (!isset($column['form'])) {
				continue;
			}
			if (isset($column['disabled']) && $column['disabled']) {
				continue;
			}
			if (isset($column['readonly']) && $column['readonly']) {
				continue;
			}
			if (array_key_exists($column['dataIndex'], $mParam)) {
				$aSets[] = $this->fieldQuote($column['dataIndex']) . '=?';
				$aParam[] = $mParam[$column['dataIndex']];
			}
		}
		$aSql[] = 'SET ' . implode(',', $aSets);
		$aSql[] = 'WHERE ' . $this->fieldQuote($pageConfig['table']['rowKey']) . '=?';
		$aParam[] = $id;
		$sSql = implode("\r\n", $aSql);
		return array($sSql, $aParam);
	}

	private function buildSqlDelete($pageConfig, $ids)
	{
		$aSql = [];
		$aSql[] = 'DELETE FROM ' . $pageConfig['sql']['from'];
		$aIns = array_fill(0, count($ids), '?');
		$aSql[] = 'WHERE ' . $this->fieldQuote($pageConfig['table']['rowKey']) . ' IN(' . implode(',', $aIns) . ')';
		$sSql = implode("\r\n", $aSql);
		return array($sSql, $ids);
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
		$sSql = $this->buildSqlSelect($data);
		$dataSource =  $this->fetchAll($sSql, $data['sql']['param']);

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

	public function tableReader($pageConfig)
	{
		$action = isset($_GET['action']) ? $_GET['action'] : '';

		if ($action === 'init') {
			$pageConfig['table'] = array_merge_recursive($pageConfig['table'], $this->table_action_list($pageConfig));
			unset($pageConfig['sql']);
			foreach ($pageConfig['table']['columns'] as &$column) {
				unset($column['sql_select']);
			}
			if (isset($pageConfig['table']['pagination'])) {
				unset($pageConfig['table']['pagination']['pageSizeDefault']);
				unset($pageConfig['table']['pagination']['pageSizeMax']);
			}
			return $pageConfig;
		}

		if (in_array($action, ['view', 'edit'])) {
			if (!isset($_GET['id'])) {
				return;
			}
			$id = $_GET['id'];
			$formModel = $this->fetchOne($pageConfig, $id);
			return [
				'formModel' => $formModel,
			];
		}

		if ($action === 'list') {
			return [
				'table' => $this->table_action_list($pageConfig),
			];
		}

		if ($action === 'create') {
			$data = file_get_contents('php://input');
			$decodedData = json_decode($data, true);
			list($sSql, $aParam) = $this->buildSqlCreate($pageConfig, $decodedData);
			$this->begin();
			$this->execute($sSql, $aParam);
			$this->commit();
			return [
				'message' => ['type' => 'success', 'content' => 'Create successful'],
				'table' => $this->table_action_list($pageConfig),
			];
		}

		if ($action === 'update') {
			$id = isset($_GET['id']) ? $_GET['id'] : '';
			$data = file_get_contents('php://input');
			$decodedData = json_decode($data, true);
			if (empty($decodedData)) {
				return [
					'message' => ['type' => 'error', 'content' => 'post cant empty'],
				];
			}
			list($sSql, $aParam) = $this->buildSqlUpdate($pageConfig, $decodedData, $id);
			$this->begin();
			$this->execute($sSql, $aParam);
			$this->commit();
			return [
				'message' => ['type' => 'success', 'content' => 'Update successful'],
				'table' => $this->table_action_list($pageConfig),
			];
		}

		if ($action === 'delete') {
			$ids = call_user_func(function () {
				$ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : '';
				$mId = [];
				foreach ($ids as $id) {
					if ($id === '') {
						continue;
					}
					$mId[$id] = 1;
				}
				return array_keys($mId);
			});
			if (count($ids) === 0) {
				return [
					'message' => ['type' => 'error', 'content' => '[ids] cant empty'],
				];
			}
			list($sSql, $aParam) =  $this->buildSqlDelete($pageConfig, $ids);
			$this->begin();
			$this->execute($sSql, $aParam);
			$this->commit();
			return [
				'message' => ['type' => 'success', 'content' => 'Delete successful'],
				'table' => $this->table_action_list($pageConfig),
			];
		}
	}
}
