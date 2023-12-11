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

async function vue_index_main(Vue, VueRouter, antd) {
  const { createApp } = Vue;
  const { createRouter, createWebHistory } = VueRouter;
  const oTopRoute = { children: await (await fetch('/api/public/route')).json() }
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
  for (var s in antd) {
    // 加载 antd 的全部组件
    const o = antd[s];
    if (o.install && o.setup) {
      app.use(o);
    }
  }
  app.mount('#app');
}

export { fetchDataByPathname, routes_filter, vue_index_main };
