const koaRouter = require("koa-router");
const fs = require('fs/promises');
const router = new koaRouter({ prefix: "/api/public" }); //后面所有的地址都会拼上 /users这个前缀

router.get(`/route.php`, async (ctx, next) => {
	// 获取文件的最后修改时间
	const filePath = './router/router-view.json';
	ctx.set('Last-Modified', (await fs.stat(filePath)).mtime.toUTCString());
	const router_view = JSON.parse((await fs.readFile(filePath)).toString());
	ctx.body = [router_view];
});

module.exports = router;
