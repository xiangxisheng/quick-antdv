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
    const form = ref({});
    const insertOpen = ref(false);
    const handleInsert = async () => {
      insertOpen.value = true;
    }
    const handleInsertSubmit = async () => {
      loading.value = true;
      await delay(1000);
      loading.value = false;
      insertOpen.value = false;
    }
    const handleDelete = async () => {
      loading.value = true;
      await delay(1000);
      loading.value = false;
      messageApi.info('handleDelete!');
    }
    const rules = {
      name: [{ required: true, message: 'Please enter user name' }],
      url: [{ required: true, message: 'please enter url' }],
      owner: [{ required: true, message: 'Please select an owner' }],
      type: [{ required: true, message: 'Please choose the type' }],
      approver: [{ required: true, message: 'Please choose the approver' }],
      dateTime: [{ required: true, message: 'Please choose the dateTime', type: 'object' }],
      description: [{ required: true, message: 'Please enter url description' }],
    };
    return {
      state,
      dataSource,
      columns,
      pagination,
      loading,
      searchInput,
      insertOpen,
      form,
      rules,
      handleSearch,
      handleReset,
      handleTableChange,
      handleInsert,
      handleInsertSubmit,
      handleDelete,
    }
  },
})