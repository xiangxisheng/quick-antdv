window.QADV = ((config) => {
	function stringifyQuery(queryObj) {
		if (!queryObj) {
			return '';
		}

		const pairs = [];
		Object.keys(queryObj).forEach(key => {
			const value = queryObj[key];
			if (Array.isArray(value)) {
				value.forEach(item => {
					pairs.push(`${encodeURIComponent(key)}=${encodeURIComponent(item)}`);
				});
			} else {
				pairs.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`);
			}
		});

		return pairs.join('&');
	};

	async function backendFetch(options) {
		const { path, param, post, dataType } = options;
		const url = new URL(location.href);
		url.pathname = path;

		if (true) {
			while (url.searchParams.keys().next().value) {
				url.searchParams.delete(url.searchParams.keys().next().value);
			}
		}

		if (param) {
			Object.keys(param).forEach(key => {
				url.searchParams.set(key, param[key]);
			});
		}

		const reqOption = {
			method: post ? 'POST' : 'GET',
			headers: {
				clientid: (function () {
					if (!window.localStorage.hasOwnProperty('clientid')) {
						window.localStorage.clientid = new Date().getTime() + '.' + Math.random().toString().substring(2);
					}
					return window.localStorage.clientid;
				})(),
			},
		};
		if (post) {
			reqOption.body = (() => {
				if (dataType === 'json') {
					return JSON.stringify(post);
				}
				else {
					const formData = new FormData();
					for (const [key, value] of Object.entries(post)) {
						formData.append(key, value);
					}
					return formData;
				}
			})();
		}
		const request = new Request(url, reqOption);

		const oResponse = await fetch(request);
		if (oResponse.status !== 200) {
			const ex = `HTTP Status: ${oResponse.statusText}`;
			ex.res = oResponse;
			throw ex;
		}
		if (config.setting.delay) {
			await delay(config.setting.delay);
		}
		const text = await oResponse.text();
		try {
			const data = JSON.parse(text);
			if (data.message && options.messageApi) {
				options.messageApi.open(data.message);
			}
			if (data.router && options.router) {
				setTimeout(() => {
					options.router.push(data.router);
				}, 1000);
			}
			return data;
		} catch (ex) {
			// JSON解析失败时把解析前的文本放进去
			ex.text = text;
			throw ex;
		}
	};

	async function backendApi(options) {
		options.path = `${config.setting.api_root}${options.path}${config.setting.api_ext}`;
		return await backendFetch(options);
	}

	async function routes_filter(route, func, parent = '') {
		const parents = [parent];
		if (route.name) {
			parents.push(route.name);
		}
		const mRet = await func(parent, route);
		if (!mRet) {
			return;
		}
		if (route.children) {
			mRet.children = [];
			for (const subroute of route.children) {
				const oSub = await routes_filter(subroute, func, parents.join('/'));
				if (oSub) {
					mRet.children.push(oSub);
				}
			}
		}
		return mRet;
	}

	async function VueCreateApp(Vue, VueRouter) {
		const { createApp } = Vue;
		const { createRouter, createWebHistory } = VueRouter;
		const { createPinia } = Pinia;
		const children = config.routes;
		const oTopRoute = { children }
		const routes = (await routes_filter(oTopRoute, async (sParent, mRoute) => {
			const item = {};
			item.meta = {};
			if (typeof (mRoute.label) === 'string') {
				item.meta.label = mRoute.label;
			}
			if (typeof (mRoute.path) === 'string') {
				item.path = mRoute.path;
			}
			if (typeof (mRoute.name) === 'string') {
				item.name = sParent + '/' + mRoute.name;
				item.path = (sParent.indexOf('/') === -1 ? '/' : '') + mRoute.name;
			}
			if (mRoute.component) {
				// 路由懒加载(https://router.vuejs.org/zh/guide/advanced/lazy-loading.html)
				mRoute.setting = config.setting;
				item.component = async () => (await import(`${config.setting.component_dir}/${mRoute.component}${config.setting.component_ext}`)).default(mRoute);
			}
			if (mRoute.alias) {
				item.alias = mRoute.alias;
			}
			return item;
		})).children;
		//routes.push({ path: '/:pathMatch(.*)', component: NotFoundComponent });
		const router = createRouter({
			history: createWebHistory(),
			routes, // `routes: routes` 的缩写
		});
		const pinia = createPinia();
		const app = createApp();
		app.use(router);
		app.use(pinia);
		app.mount('#app');
		app.provide('i18n', i18n(config));
		return { app, router };
	}

	const delay = ms => new Promise((resolve) => setTimeout(resolve, ms))

	const storage = (key, object) => {
		let data = (() => {
			const str = object.getItem(key);
			if (str === null || typeof (str) !== 'string' || str === '') {
				return {};
			}
			try {
				return JSON.parse(str);
			} catch (e) { }
			return {};
		})();
		return {
			set(name, value) {
				data[name] = value;
			},
			del(name) {
				delete data[name];
			},
			has(name) {
				return data.hasOwnProperty(name);
			},
			get(name) {
				return data[name];
			},
			save() {
				object.setItem(key, JSON.stringify(data));
			},
			clear() {
				object.removeItem(key);
			},
		};
	};

	const stateStorage = (key) => {
		return storage(key, sessionStorage);
	};

	const loadCSS = (file, cb) => {
		const link = document.createElement('link');
		link.rel = 'stylesheet';
		link.href = file;
		link.onload = () => {
			if (typeof (cb) === 'function') {
				cb();
			}
		};
		document.head.appendChild(link);
	};

	const loadJS_one = (file, cb) => {
		const script = document.createElement('script');
		script.src = file;
		script.onerror = (event) => {
			if (typeof (cb) === 'function') {
				cb(event);
			}
		}
		script.onload = (event) => {
			if (typeof (cb) === 'function') {
				cb(event);
			}
		};
		document.head.appendChild(script);
	};

	const loadJS_all = async (files, resolve, reject) => {
		let c = 0;
		for (const file of files) {
			loadJS_one(file, (event) => {
				if (event.type === "error") {
					reject(`Oops! The file failed to load.<br>${file}`);
				}
				c++;
				if (c === files.length) {
					if (typeof (resolve) === 'function') {
						resolve();
					}
				}
			});
		}
	};

	const loadJS = files => new Promise((resolve, reject) => loadJS_all(files, resolve, reject));

	const isDev = () => {
		if (config.setting.isDev) {
			return true;
		}
		const devHost = [];
		devHost.push('localhost');
		devHost.push('127.0.0.1');
		return devHost.indexOf(location.hostname) !== -1;
	};

	const main = async () => {
		try {
			await main_do();
		} catch (ex) {
			document.getElementById('loader').style.display = 'none';
			document.getElementsByClassName('browser-upgrade')[0].style.display = '';
			document.getElementById('logo').src = config.setting.assets_dir + "/img/logo.svg";
			document.getElementsByClassName('browser-upgrade__text')[0].innerHTML = ex.toString();
			console.error(ex);
		}
	}

	const main_do = async () => {
		loadCSS(`${config.setting.assets_dir}/css/reset.min.css`);
		loadCSS(`${config.setting.assets_dir}/css/boxicons.min.css`);

		const VueGlobalFile = isDev() ? 'https://unpkg.com/vue@3/dist/vue.global.js' : `${config.setting.assets_dir}/js/vue/vue.global.prod.js`;
		await loadJS([
			VueGlobalFile,
			`${config.setting.assets_dir}/js/antd/dayjs.min.js`,
			`${config.setting.assets_dir}/js/antd/dayjs-plugin.min.js`,
			`${config.setting.assets_dir}/js/sheetjs/xlsx.full.min.js`,
		]);
		await loadJS([
			`${config.setting.assets_dir}/js/antd/antd.min.js`,
			`${config.setting.assets_dir}/js/i18n.js`,
			`${config.setting.assets_dir}/js/vue/vue-demi.iife.js`,
			`${config.setting.assets_dir}/js/vue/vue-router.global.prod.js`,
		]);
		await loadJS([
			`${config.setting.assets_dir}/js/vue/pinia.iife.prod.js`,
		]);
		const { router } = await VueCreateApp(Vue, VueRouter);

		firadio.router = router;
		firadio.router.curQuery = () => router.currentRoute.value.query;
		firadio.router.push_query = async (_query) => {
			const curQuery = router.currentRoute.value.query;
			const query = {};
			for (const k in curQuery) {
				query[k] = curQuery[k];
			}
			for (const k in _query) {
				query[k] = _query[k];
			}
			router.push({ query });
		}
		document.getElementById('loader').style.display = 'none';
	};

	function deepCloneObject(source) {
		if (typeof source !== "object" || source === null) return source;

		const target = Array.isArray(source) ? [] : {};

		for (var key in source) {
			if (Object.prototype.hasOwnProperty.call(source, key)) {
				target[key] = deepCloneObject(source[key]);
			}
		}

		return target;
	}

	function array_set_recursive(target, source) {
		if (typeof target !== typeof source) {
			return source;
		}
		if (typeof source !== 'object' || source === null) {
			return source;
		}
		if (Array.isArray(source)) {
			return deepCloneObject(source);
		}
		Object.keys(source).forEach((key) => {
			target[key] = array_set_recursive(target[key], source[key]);
		});
		return target;
	}

	function array_merge_recursive() {
		const argsArray = Array.from(arguments);
		const argvFirst = argsArray.shift();
		const target = Array.isArray(argvFirst) ? [] : {};
		for (const argvOne of argsArray) {
			array_set_recursive(target, argvOne);
		}
		return target;
	}

	function filterNullItem(items) {
		for (const k in items) {
			const item = items[k];
			if (item === null) {
				delete items[k];
			}
		}
	};

	function tryParseJSON(str) {
		try {
			return JSON.parse(str);
		} catch (e) { }
		return {};
	};

	const firadio = {};
	firadio.backendApi = backendApi;
	firadio.routes_filter = routes_filter;
	firadio.delay = delay;
	firadio.stateStorage = stateStorage;
	firadio.main = main;
	firadio.deepCloneObject = deepCloneObject;
	firadio.array_set_recursive = array_set_recursive;
	firadio.array_merge_recursive = array_merge_recursive;
	firadio.filterNullItem = filterNullItem;
	firadio.tryParseJSON = tryParseJSON;
	return firadio;
});
