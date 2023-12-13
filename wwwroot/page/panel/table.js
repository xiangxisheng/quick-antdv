import { fetchDataByPathname } from 'firadio';
const { ref, reactive } = Vue;
const { Table, Input, Button } = antd;
export default async () => ({
  template: await (await fetch('./page/panel/table.htm')).text(),
  components: {
    ATable: Table,
    AInput: Input,
    AButton: Button,
  },
  setup() {
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
    const pagination = reactive({
      total: 200,
      current: 2,
      pageSize: 20,
    });
    const loading = ref(false);
    const dataSource = ref([]);
    const searchInput = ref('');
    const fetchData = async () => {
      const data = await fetchDataByPathname('api/table', { keyspace_name: 'test', table_name: 'test' });
      pagination.total = 100;
      columns.value.length = 0;
      for (const col of data.columns) {
        const column = { title: col.name, dataIndex: col.name, key: col.name };
        column.customFilterDropdown = true;
        column.onFilterDropdownOpenChange = visible => {
          if (visible) {
            setTimeout(() => {
              searchInput.value.focus();
            }, 100);
          }
        };
        columns.value.push(column);
      }
      columns.value.push({
        title: 'Action',
        key: 'operation',
        fixed: 'right',
        width: 100,
      });
      dataSource.value = data.rows;
    };
    const handleTableChange = async (pag, filters, sorter) => {
      fetchData();
      pagination.current = pag.current;
      pagination.pageSize = pag.pageSize;
      console.log(pag, filters, sorter);
    }
    return {
      state,
      dataSource,
      columns,
      pagination,
      loading,
      searchInput,
      handleSearch,
      handleReset,
      handleTableChange,
    }
  },
})