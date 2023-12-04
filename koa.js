const Koa = require('koa');
const KoaRouter = require('koa-router');
const KoaStatic = require('koa-static');
const KoaSendfile = require('koa-sendfile');

const fs = require('fs');
const cassandra = require('cassandra-driver');

// 配置 Cassandra 客户端
const client = new cassandra.Client({
  //contactPoints: ['192.168.1.3'],
  //contactPoints: ['121.89.210.35'],
  contactPoints: ['47.90.101.1'],
  protocolOptions: { port: 9042 },
  localDataCenter: 'dc1',
  keyspace: 'test',
  credentials: { username: 'test', password: 'test' },
});

const app = new Koa();
app.use(async (ctx, next) => {
  ctx.client = client;
  try {
    await next();
  } catch (e) {
    ctx.body = e;
  }
});
app.use(require("./router/api").routes());
app.use(KoaStatic('wwwroot', {
  index: 'index.html',
  hidden: false, // 是否同意传输隐藏文件
  defer: false, // 如果为true，则在返回next()之后进行服务，从而允许后续中间件先进行响应
  // 当defer配置为false时，只要文件存在就会直接读取并响应相应的文件，而不会经过API中间件的处理了
  // 建议将defer配置为false让他只处理纯静态
}));
app.use(async (ctx, next) => {
  await KoaSendfile(ctx, '../dist/index.html');
});

const port = 3000;
app.listen(port, () => {
  console.log(`Server running on http://localhost:${port}`);
});
