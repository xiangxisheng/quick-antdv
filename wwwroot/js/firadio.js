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

  async function fetchDataByPathname(_pathname, _param) {
    const url = `${_pathname}?` + stringifyQuery(_param);
    try {
      const oResponse = await fetch(url);
      if (oResponse.status !== 200) {
        return { message: oResponse.statusText };
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
    const oTopRoute = { children: await (await fetch('/api/public/route.php')).json() }
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
        item.component = async () => (await import(`/page/${mRoute.component}.js`)).default(mRoute);
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

  const firadio = {};
  firadio.fetchDataByPathname = fetchDataByPathname;
  firadio.routes_filter = routes_filter;
  firadio.delay = delay;
  firadio.stateStorage = stateStorage;
  firadio.main = main;
  return firadio;
})();
