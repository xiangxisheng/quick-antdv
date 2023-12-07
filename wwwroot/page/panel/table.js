import { fetchDataByPathname } from 'firadio';
export default async (param) => {
  return {
    template: await (await fetch('./page/panel/table.htm')).text(),
    data() {
      return {
        dataSource: [],
        columns: [],
      }
    },
    async created() {
      const data = await this.fetchDataByPathname('api/table', { keyspace_name: 'test', table_name: 'test' });
      this.columns.length = 0;
      for (const col of data.columns) {
        this.columns.push({ title: col.name, dataIndex: col.name, key: col.name });
      }
      this.dataSource.length = 0;
      for (const row of data.rows) {
        this.dataSource.push(row);
      }
    },
    methods: {
      async fetchDataByPathname(_pathname, _param) {
        const ret = await fetchDataByPathname(_pathname, _param);
        return ret;
      },
    },
  }
}