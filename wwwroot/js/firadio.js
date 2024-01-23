window.firadio = (() => {
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

  async function backendApi(options) {
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

    try {
      const oResponse = await fetch(request);
      if (oResponse.status !== 200) {
        return { message: oResponse.statusText };
      }
      if (location.hostname === '127.0.0.2') {
        await delay(500);
      }
      return await oResponse.json();
    } catch (e) {
      return e;
    }

  };

  async function routes_filter(route, func, parent = '') {
    const parents = [parent];
    if (route.name) {
      parents.push(route.name);
    }
    const mRet = await func(parent, route);
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
    const children = await backendApi({ path: '/api/public/route.php' });
    const oTopRoute = { children }
    const routes = (await routes_filter(oTopRoute, async (sParent, mRoute) => {
      const item = {};
      if (typeof (mRoute.path) === 'string') {
        item.path = mRoute.path;
      }
      if (typeof (mRoute.name) === 'string') {
        item.name = sParent + '/' + mRoute.name;
        item.path = (sParent.indexOf('/') === -1 ? '/' : '') + mRoute.name;
      }
      if (mRoute.component) {
        // 路由懒加载(https://router.vuejs.org/zh/guide/advanced/lazy-loading.html)
        item.component = async () => (await import(`/${mRoute.component}.js`)).default(mRoute);
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
    const app = createApp();
    app.use(router);
    app.mount('#app');
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
    script.onload = () => {
      if (typeof (cb) === 'function') {
        cb();
      }
    };
    document.head.appendChild(script);
  };

  const loadJS_all = (files, cb) => {
    let c = 0;
    for (const file of files) {
      loadJS_one(file, () => {
        c++;
        if (c === files.length) {
          if (typeof (cb) === 'function') {
            cb();
          }
        }
      });
    }
  };

  const loadJS = files => new Promise((resolve) => loadJS_all(files, resolve));

  const isDev = () => {
    const devHost = [];
    devHost.push('localhost');
    devHost.push('127.0.0.1');
    return devHost.indexOf(location.hostname) !== -1;
  };

  const main = async () => {

    loadCSS('./css/reset.min.css');
    loadCSS('./css/boxicons.min.css');

    const VueGlobalFile = isDev() ? 'https://unpkg.com/vue@3/dist/vue.global.js' : './js/vue/vue.global.prod.js';
    await loadJS([VueGlobalFile, './js/antd/dayjs.min.js', './js/antd/dayjs-plugin.min.js']);
    await loadJS(['./js/vue/vue-router.global.prod.js', './js/antd/antd.min.js']);
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
})();
