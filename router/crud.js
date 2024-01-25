module.exports = (router, path, cql) => {
	router.get(path, async (ctx, next) => {
		const pageSize = 100;
		const q = ctx.request.query;
		const result = await ctx.client.execute(cql, [q.keyspace_name, q.table_name]);
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
};