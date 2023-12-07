const koaRouter = require("koa-router");
const router = new koaRouter({ prefix: "/api/public" }); //后面所有的地址都会拼上 /users这个前缀

router.get(`/route`, async (ctx, next) => {
  ctx.body = {
    name: '',
    label: '首页',
    component: 'home',
    role: 'public',
    children: [
      {
        name: 'sign',
        label: '用户登录',
        component: 'sign',
        role: 'public',
      },
      {
        name: 'panel',
        label: '面板',
        component: 'panel',
        role: 'user',
        children: [
          {
            name: 'index',
            label: '面板首页',
            component: 'panel/index',
            role: 'user',
          },
          {
            name: 'data',
            label: '数据查询',
            component: 'data',
            role: 'user',
            children: [
              {
                name: 'table1',
                label: '测试表1',
                component: 'panel/table',
                role: 'user',
              },
              {
                name: 'table2',
                label: '测试表2',
                component: 'panel/table',
                role: 'user',
              },
            ]
          }
        ]
      },
    ]
  };
});


module.exports = router;
