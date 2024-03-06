<?php

namespace QuickPHP;

use Exception;

class Auth
{
	private $oConfig;

	public function __construct($oConfig)
	{
		$this->oConfig = $oConfig;
	}

	private function pdo()
	{
		return $this->oConfig->pdo();
	}

	public function route_path()
	{
		// 取得当前访问的route(路由)路径
		$path = $_SERVER['SCRIPT_NAME'];
		$api_root = $this->oConfig->getSetting('api_root');
		$api_ext = $this->oConfig->getSetting('api_ext');
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
		$this->routes($this->oConfig->getRoutes(), '', function ($name, $route) use (&$roles) {
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
		// 取得加密后的sessid，用于存储到数据库
		// 采用这种方法能防止模拟会话、防止用户提交异常数据
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

	private function response($message, $errno = 0, $router_path = null)
	{
		$response = [
			'errno' => $errno,
			'message' => [
				'type' => $errno === 0 ? 'success' : 'error',
				'content' => $message,
			],
		];
		if ($router_path) {
			$response['router'] = ['path' => $router_path];
		}
		if ($errno !== 0) {
			exit(json_encode($response));
		}
		return $response;
	}

	private function user_uid()
	{
		$sessid = $this->sessid();
		$mRowSession = $this->pdo()->fetch('SELECT uid,status FROM {table_pre}system_sessions WHERE sessid=:sessid', ['sessid' => $sessid]);
		if (!$mRowSession) {
			// 用户未登录过
			$this->response('请先登录', 401, '/login');
		}
		if ($mRowSession['status'] != 1) {
			// 已退出登录
			$this->response('您已退出登录', 401, '/login');
		}
		return $mRowSession['uid'];
	}

	public function user_roles()
	{
		// 取得当前用户所拥有的角色
		$uid = $this->user_uid();
		if (!$uid) {
			$this->response('401:unauthorized', 401, '/login');
		}
		$mRowUser = $this->pdo()->fetch('SELECT roles FROM {table_pre}system_users WHERE uid=:uid', ['uid' => $uid]);
		if (!$mRowUser) {
			$this->response('用户不存在，请重新登录', 401, '/login');
		}
		return explode(',', $mRowUser['roles']);
	}

	public function check()
	{
		// 检查当前路径是否有权限访问
		// 1:检查当前路径是否在route中定义过role
		$route_role = $this->route_role();
		if (empty($route_role)) {
			// 没定义role的不允许访问
			$this->response('not found in routes', 400);
		}
		if ($route_role === 'public') {
			return;
		}
		// 2:取得当前用户所拥有的角色
		$user_roles = $this->user_roles();
		if (!in_array($route_role, $user_roles)) {
			$this->response('no permission', 403);
		}
	}

	private function token_new()
	{
		// 产生一个由时间戳与随机数组成的token
		$mt = explode(' ', microtime());
		return $mt[1] . '.' . substr($mt[0], 2, 6) . '.' . mt_rand();
	}

	public function login($post)
	{
		// 用户登录
		$mRowUser = $this->pdo()->fetch('SELECT uid,password FROM {table_pre}system_users WHERE username=:username', ['username' => $post['username']]);
		if (empty($mRowUser)) {
			throw new Exception("您输入的用户名【{$post['username']}】不存在");
		}
		if ($mRowUser['password'] === $post['password']) {
		}
		// 假如密码校验成功，开始登录
		$ipaddr = $_SERVER['REMOTE_ADDR'];
		$this->session_login($mRowUser['uid'], $ipaddr);
		return $this->response('登录成功');
	}

	public function session_login($uid, $ipaddr)
	{
		$sessid = $this->sessid();
		$token = $this->token_new();
		setcookie('token', $token, time() + 86400 * 30, '/', '', false, true);
		$mData = [
			'uid' => $uid,
			'token' => $this->hash($token),
			'status' => 1,
			'ipaddr_last' => $ipaddr,
			'updatedAt' => date('Y-m:d H:i:s'),
			'logined' => ['CURRENT_TIMESTAMP(6)'],
			'updated' => ['CURRENT_TIMESTAMP(6)'],
		];
		$mRowSession = $this->pdo()->fetch('SELECT sessid FROM {table_pre}system_sessions WHERE sessid=:sessid', ['sessid' => $sessid]);
		if (empty($mRowSession)) {
			$mData['sessid'] = $sessid;
			$mData['createdAt'] = date('Y-m:d H:i:s');
			$mData['ipaddr_first'] = $ipaddr;
			$this->pdo()->insert('{table_pre}system_sessions', $mData);
		} else {
			$this->pdo()->update('{table_pre}system_sessions', $mData, ['sessid' => $sessid]);
		}
	}
}
