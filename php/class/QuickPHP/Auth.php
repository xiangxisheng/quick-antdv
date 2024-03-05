<?php

namespace QuickPHP;

use Exception;

class Auth
{
	private $mConf;

	public function __construct($mConf)
	{
		$this->mConf = $mConf;
	}

	public function route_path()
	{
		// 取得当前访问的route(路由)路径
		$path = $_SERVER['SCRIPT_NAME'];
		$api_root = $this->mConf['setting']['api_root'];
		$api_ext = $this->mConf['setting']['api_ext'];
		if (substr($path, 0, strlen($api_root)) !== $api_root) {
			return;
		}
		$path = substr($path, strlen($api_root), -strlen($api_ext));
		return $path;
	}

	public function routes($routes, $name, $callback)
	{
		// 递归取得路由配置
		foreach ($routes as $route) {
			$newName = isset($route['name']) ? "{$name}/{$route['name']}" : $name;
			$callback($newName, $route);
			if (isset($route['children'])) {
				$this->routes($route['children'], $newName, $callback);
			}
		}
	}

	public function route_roles()
	{
		// 取得每个route_path对应所需的role(角色)
		$roles = [];
		$this->routes($this->mConf['routes'], '', function ($name, $route) use (&$roles) {
			if (isset($route['role'])) {
				$roles[$name] = $route['role'];
			}
		});
		return $roles;
	}

	public function hash($str)
	{
		// md5加密字符串后再base64
		$str = base64_encode(md5($str, true));
		$str = str_replace('+', '', $str);
		$str = str_replace('/', '', $str);
		$str = str_replace('=', '', $str);
		return substr($str, 0, 16);
	}

	public function sessid()
	{
		// 取得加密后的sessid
		return $this->hash($_SERVER['HTTP_CLIENTID']);
	}

	public function route_role()
	{
		// 取得当前路径所需的role
		$route_roles = $this->route_roles();
		$route_path = $this->route_path();
		if (!isset($route_roles[$route_path])) {
			return;
		}
		return $route_roles[$route_path];
	}

	public function user_roles()
	{
		// 取得当前用户所拥有的角色
	}

	public function check()
	{
		// 检查当前路径是否有权限访问
		$response = [
			'errno' => 0,
			'message' => [
				'type' => 'error',
				'content' => '',
			],
		];
		$route_role = $this->route_role();
		if (empty($route_role)) {
			$json['errno'] = 400;
			exit(json_encode($response));
		}
	}

	public function login($db, $post)
	{
		$response = [
			'errno' => 0,
			'message' => [
				'type' => 'success',
				'content' => '',
			],
		];
		// 用户登录
		$mRowUser = $db->fetch('SELECT password FROM {table_pre}system_users WHERE username=:username', ['username' => $post['username']]);
		if (empty($mRowUser)) {
			throw new Exception("您输入的用户名【{$post['username']}】不存在");
		}
		if ($mRowUser['password'] === $post['password']) {
		}
		// 假如密码校验成功，开始登录
		$response['message']['content'] = '登录成功';
		return $response;
	}
}
