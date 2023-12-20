const koaRouter = require("koa-router");
const router = new koaRouter({ prefix: "/api" }); //后面所有的地址都会拼上 /users这个前缀

router.get(`/keyspaces`, async (ctx, next) => {
  const result = await ctx.client.execute('SELECT keyspace_name,replication FROM system_schema.keyspaces');
  ctx.body = {};
  ctx.body.rows = result.rows;
  ctx.body.columns = result.columns;
});

router.get(`/tables`, async (ctx, next) => {
  const q = ctx.request.query;
  const cql = `SELECT table_name FROM system_schema.tables WHERE keyspace_name=?`;
  const result = await ctx.client.execute(cql, [q.keyspace_name]);
  ctx.body = {};
  ctx.body.rows = result.rows;
  ctx.body.columns = ((columns) => {
    const ret = [];
    for (const column of columns) {
      const col = { title: column.name, dataIndex: column.name, key: column.name };
      ret.push(col);
    }
    ret.push({
      title: 'Action',
      fixed: 'right',
      width: 100,
      actions: [
        { title: '表结构' }
      ]
    });
    return ret;
  })(result.columns);
});

router.get(`/columns`, async (ctx, next) => {
  const pageSize = 100;
  const q = ctx.request.query;
  const cql = `SELECT column_name,type,kind,position FROM system_schema.columns WHERE keyspace_name=? AND table_name=? LIMIT ${pageSize}`;
  const result = await ctx.client.execute(cql, [q.keyspace_name, q.table_name]);
  console.log(result);
  ctx.body = {};
  ctx.body.info = {};
  ctx.body.info.rowKey = 'column_name';
  ctx.body.pagination = {
    total: result.rowLength,
    current: 1,
    pageSize,
  };
  ctx.body.rows = result.rows;
  ctx.body.columns = ((columns) => {
    const ret = [];
    for (const column of columns) {
      const col = { title: column.name, dataIndex: column.name, key: column.name };
      ret.push(col);
    }
    ret.push({
      title: 'Action',
      fixed: 'right',
      width: 100,
      actions: [
        { title: '删除' }
      ]
    });
    return ret;
  })(result.columns);
});

router.get(`/table`, async (ctx, next) => {
  const q = ctx.request.query;
  const cql = `SELECT * FROM "${q.table_name}"`;
  const result = await ctx.client.execute(cql, []);
  ctx.body = {};
  ctx.body.rows = result.rows;
  ctx.body.columns = result.columns;
});

module.exports = router;
