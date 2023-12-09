import { fetchDataByPathname } from 'firadio';
export default async (param) => {
  return {
    template: await (await fetch('./page/panel/table.htm')).text(),
    data() {
      return {
        state: {
          searchText: '',
          searchedColumn: '',
        },
        dataSource: [],
        columns: [],
      }
    },
    async created() {
      const data = await this.fetchDataByPathname('api/table', { keyspace_name: 'test', table_name: 'test' });
      this.columns.length = 0;
      for (const col of data.columns) {
        const column = { title: col.name, dataIndex: col.name, key: col.name };
        column.customFilterDropdown = true;
        column.onFilter = (value, record) => {
          return record.name.toString().toLowerCase().includes(value.toLowerCase());
        };
        column.onFilterDropdownOpenChange = visible => {
          if (visible) {
            setTimeout(() => {
              this.$refs.searchInput.focus();
            }, 100);
          }
        };
        this.columns.push(column);
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
      async handleSearch(selectedKeys, confirm, dataIndex) {
        confirm();
        this.state.searchText = selectedKeys[0];
        this.state.searchedColumn = dataIndex;
      },
      async handleReset(clearFilters) {
        clearFilters({
          confirm: true,
        });
        this.state.searchText = '';
      },
    },
  }
}