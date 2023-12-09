// 加载Koa相关模块
const Koa = require('koa');
const KoaRouter = require('koa-router');
const KoaStatic = require('koa-static');
const KoaSendfile = require('koa-sendfile');
const KoaCompress = require('koa-compress');

// 加载其他模块
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

// 调用Koa
const app = new Koa();

// 错误处理
app.use(async (ctx, next) => {
  ctx.client = client;
  try {
    await next();
  } catch (e) {
    ctx.body = e;
  }
});

// 为文本类型的文件启用压缩功能
const compress_list = [];
compress_list.push('text/html');
compress_list.push('text/css');
compress_list.push('application/javascript');
compress_list.push('application/json');
compress_list.push('image/vnd.microsoft.icon');
compress_list.push('image/svg+xml');
app.use(KoaCompress({
  filter(content_type) {
    if (compress_list.indexOf(content_type) !== -1) {
      return true;
    }
    console.log(`Not Compress content_type=${content_type}`);
    return false;
  },
  threshold: 2048,
  gzip: {
    flush: require('zlib').constants.Z_SYNC_FLUSH
  },
  deflate: {
    flush: require('zlib').constants.Z_SYNC_FLUSH,
  },
  br: false // disable brotli
}));

// 添加中间件来处理304响应
app.use(async (ctx, next) => {
  await next();
  if (ctx.fresh) {
    // 没有修改，返回304
    ctx.status = 304;
  }
});

// 加载路由
app.use(require("./router/public").routes());
app.use(require("./router/api").routes());

// 加载静态网站
app.use(KoaStatic('wwwroot', {
  index: 'index.html',
  hidden: false, // 是否同意传输隐藏文件
  defer: false, // 如果为true，则在返回next()之后进行服务，从而允许后续中间件先进行响应
  // 当defer配置为false时，只要文件存在就会直接读取并响应相应的文件，而不会经过API中间件的处理了
  // 建议将defer配置为false让他只处理纯静态
  maxage: 1000 * 5,
}));

// 加载404页面
app.use(async (ctx, next) => {
  await KoaSendfile(ctx, 'wwwroot/index.html');
});

// 最后启动服务端
const port = 3000;
app.listen(port, () => {
  console.log(`Server running on http://localhost:${port}`);
});
