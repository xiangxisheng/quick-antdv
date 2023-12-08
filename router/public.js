const koaRouter = require("koa-router");
const fs = require('fs/promises');
const router = new koaRouter({ prefix: "/api/public" }); //后面所有的地址都会拼上 /users这个前缀

router.get(`/route`, async (ctx, next) => {
  const router_view = JSON.parse((await fs.readFile('./router/router-view.json')).toString());
  ctx.body = [router_view];
});


module.exports = router;
