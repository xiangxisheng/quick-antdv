import { fetchDataByPathname } from 'firadio';
const { ref, reactive } = Vue;
const { usePagination } = VueRequest;
const { Table, Input, Button } = antd;
export default async () => ({
  template: await (await fetch('./page/panel/table.htm')).text(),
  components: {
    ATable: Table,
    AInput: Input,
    AButton: Button,
  },
  setup() {
    // https://www.attojs.org/guide/documentation/pagination.html#example
    // const queryData = async (params) => {
    //   console.log(params);
    // };
    // const aa = usePagination(queryData, {
    //   formatResult: res => {
    //     console.log('res', res);
    //   },
    //   defaultParams: [
    //     {
    //       limit: 5,
    //     },
    //   ],
    //   pagination: {
    //     currentKey: 'page',
    //     pageSizeKey: 'results',
    //   },
    // });
    // console.log(aa);
    const state = reactive({
      searchText: '',
      searchedColumn: '',
    });
    const handleSearch = (selectedKeys, confirm, dataIndex) => {
      confirm();
      state.searchText = selectedKeys[0];
      state.searchedColumn = dataIndex;
    };
    const handleReset = (clearFilters) => {
      clearFilters({
        confirm: true,
      });
      state.searchText = '';
    };
    const columns = ref([]);
    const dataSource = ref([]);
    const searchInput = ref();
    (async () => {
      const data = await fetchDataByPathname('api/table', { keyspace_name: 'test', table_name: 'test' });
      for (const col of data.columns) {
        const column = { title: col.name, dataIndex: col.name, key: col.name };
        column.customFilterDropdown = true;
        column.onFilter = (value, record) => {
          return record.name.toString().toLowerCase().includes(value.toLowerCase());
        };
        column.onFilterDropdownOpenChange = visible => {
          if (visible) {
            setTimeout(() => {
              searchInput.value.focus();
            }, 100);
          }
        };
        columns.value.push(column);
      }
      for (const row of data.rows) {
        dataSource.value.push(row);
      }
    })();
    return {
      state,
      dataSource,
      columns,
      searchInput,
      handleSearch,
      handleReset,
    }
  },
})