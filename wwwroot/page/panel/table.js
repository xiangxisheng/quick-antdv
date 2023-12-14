import { fetchDataByPathname } from 'firadio';
import { delay } from 'firadio';
const { ref, reactive } = Vue;
const { Space, Table, Input, Button, Popconfirm, Drawer } = antd;
const { Form, FormItem, Row, Col, Textarea, DatePicker, Select, SelectOption } = antd;
const [messageApi, contextHolder] = antd.message.useMessage();

export default async () => ({
  template: await (await fetch('./page/panel/table.htm')).text(),
  components: {
    ASpace: Space,
    ATable: Table,
    AInput: Input,
    AButton: Button,
    APopconfirm: Popconfirm,
    contextHolder,
    ADrawer: Drawer,
    AForm: Form,
    AFormItem: FormItem,
    ARow: Row,
    ACol: Col,
    ATextarea: Textarea,
    ADatePicker: DatePicker,
    ASelect: Select,
    ASelectOption: SelectOption,
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
    const loading = ref(false);
    const tableState = reactive({
      columns: [],
      dataSource: [],
      rowSelection: {
        selectedRowKeys: [],
      },
      pagination: {
        total: 200,
        current: 2,
        pageSize: 20,
      },
    });
    tableState.rowSelection.onChange = (selectedRowKeys) => {
      tableState.rowSelection.selectedRowKeys = selectedRowKeys;
    };

    const searchInput = ref('');
    const fetchData = async () => {
      const data = await fetchDataByPathname('api/table', { keyspace_name: 'test', table_name: 'test' });
      tableState.pagination.total = 100;
      tableState.columns.length = 0;
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
        tableState.columns.push(column);
      }
      tableState.columns.push({
        title: 'Action',
        key: 'operation',
        fixed: 'right',
        width: 100,
      });
      tableState.dataSource = data.rows;
    };
    tableState.change = async (pag, filters, sorter) => {
      fetchData();
      tableState.pagination.current = pag.current;
      tableState.pagination.pageSize = pag.pageSize;
      console.log(pag, filters, sorter);
    }

    const drawerState = reactive({
      open: false,
      data: {},
      rules: {},
      model: {},
    });
    drawerState.finish = async () => {
      loading.value = true;
      await delay(1000);
      loading.value = false;
      drawerState.open = false;
    }
    const handleDelete = async () => {
      loading.value = true;
      await delay(1000);
      loading.value = false;
      messageApi.info(JSON.stringify(tableState.rowSelection.selectedRowKeys));
    }
    drawerState.finishFailed = (errorInfo) => {
      messageApi.error(errorInfo.errorFields[0].errors[0], 1);
    };
    drawerState.rules = {
      name: [{ required: true, message: 'Please enter user name' }],
      url: [{ required: true, message: 'please enter url' }],
      owner: [{ required: true, message: 'Please select an owner' }],
      type: [{ required: true, message: 'Please choose the type' }],
      approver: [{ required: true, message: 'Please choose the approver' }],
      dateTime: [{ required: true, message: 'Please choose the dateTime', type: 'object' }],
      description: [{ required: true, message: 'Please enter url description' }],
    };
    return {
      loading,
      tableState,
      drawerState,
      state,
      searchInput,
      handleSearch,
      handleReset,
      handleDelete,
    }
  },
})