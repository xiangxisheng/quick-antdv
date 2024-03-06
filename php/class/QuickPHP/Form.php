<?php

namespace QuickPHP;

use Exception;

class Form
{
	public function __construct()
	{
	}

	public function reader($pageConfig)
	{
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		if ($action === 'init') {
			return $pageConfig;
		}
		try {
			$data = file_get_contents('php://input');
			$decodedData = json_decode($data, true);
			return $pageConfig['onAction']($this, $action, $decodedData);
		} catch (Exception $ex) {
			return [
				'message' => [
					'type' => 'error',
					'content' => $ex->getMessage(),
				],
			];
		}
		return [
			'message' => ['type' => 'error', 'content' => 'test'],
		];
	}
}
