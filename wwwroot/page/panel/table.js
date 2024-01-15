const { fetchDataByPathname } = firadio;
const { delay } = firadio;
const { ref, reactive, watch } = Vue;
const { Space, Table, Input, Button, Popconfirm, Drawer } = antd;
const { Form, FormItem, Row, Col, Textarea, DatePicker, Select, SelectOption } = antd;
const [messageApi, contextHolder] = antd.message.useMessage();
const { useRouter, useRoute } = VueRouter;

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
    const router = useRouter();
    const route = useRoute();
    const handleSearch = (confirm) => {
      confirm();
    };
    const handleReset = (clearFilters) => {
      clearFilters({
        confirm: true,
      });
    };
    const loading = ref(false);
    const tableState = reactive({
      info: {},
      buttons: [],
      columns: [],
      dataSource: [],
      rowSelection: null,
      pagination: {
        total: 0,
        current: 1,
        pageSize: 20,
      },
    });

    const searchInput = ref('');
    const jsonTryParse = (str) => {
      try {
        return JSON.parse(str);
      } catch (e) { }
      return {};
    };
    const fetchData = async () => {
      const query = route.query;
      const path = `api${route.path}.php`;
      const param = {};
      if (query.table_name) {
        param.table_name = query.table_name;
      }
      param.pagination = query.pagination;
      param.filters = query.filters;
      param.sorter = query.sorter;
      const data = await fetchDataByPathname(path, param);
      tableState.info = data.info;
      if (data.info.rowSelection) {
        tableState.rowSelection = {
          selectedRowKeys: [],
        };
        tableState.rowSelection.onChange = (selectedRowKeys) => {
          tableState.rowSelection.selectedRowKeys = selectedRowKeys;
        };
      }
      tableState.pagination = data.pagination;
      tableState.columns.length = 0;
      const oQueryFilters = jsonTryParse(query.filters);
      const oQuerySorter = jsonTryParse(query.sorter);
      if (data.buttons) {
        tableState.buttons = data.buttons;
      }
      for (const column of data.columns) {
        if (!column.width) {
          continue;
        }
        column.search_dayjs = [];
        column.customFilterDropdown = column.sql_where ? true : false;
        if (oQueryFilters[column.dataIndex]) {
          column.filteredValue = oQueryFilters[column.dataIndex];
          if (column.type === 'date') {
            for (var i = 0; i < column.filteredValue.length; i++) {
              column.search_dayjs[i] = dayjs(column.filteredValue[i], column.format);
            }
          }
        }
        if (oQuerySorter['field'] === column.dataIndex && oQuerySorter['order']) {
          column.sortOrder = oQuerySorter['order'];
        }
        column.onFilterDropdownOpenChange = visible => {
          if (visible) {
            setTimeout(() => {
              searchInput.value.focus();
            }, 100);
          }
        };
        tableState.columns.push(column);
      }

      tableState.dataSource = data.rows;
    };
    tableState.change = async (pagination, filters, sorter) => {
      const query = {};
      query.pagination = JSON.stringify({ current: pagination.current, pageSize: pagination.pageSize });
      query.filters = JSON.stringify(filters);
      query.sorter = JSON.stringify({ order: sorter.order, field: sorter.field });
      router.push({ query });
    }
    tableState.push_query = (record) => {
      router.push({ hash: '/xx', query: { table_name: record.table_name } });
    };
    tableState.action = async (action, record) => {
      const path = `api${route.path}.php`;
      const param = { action };
      param[tableState.info.rowKey] = record[tableState.info.rowKey];
      loading.value = true;
      const data = await fetchDataByPathname(path, param);
      drawerState.action = action;
      drawerState.title = action === 'edit' ? 'Edit' : 'View';
      for (const formItem of data.formItems) {
        if (formItem.form === 'date-picker') {
          formItem.value_date = dayjs(formItem.value, formItem.format);
        }
        if (action !== 'edit') {
          formItem.readonly = true;
        }
        if (formItem.form === 'select') {
          if (formItem.readonly) {
            const options = [];
            for (const option of formItem.options) {
              if (option.value === formItem.value) {
                options.push(option);
              }
            }
            formItem.options = options;
          }
        }
      }
      if (action === 'view') {
        drawerState.maskClosable = true;
      }
      drawerState.formItems = data.formItems;
      drawerState.open = true;
      loading.value = false;
    };
    fetchData();

    const drawerState = reactive({
      open: false,
      maskClosable: false,
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
    watch(
      () => route.query,
      () => {
        fetchData();
      }
    );
    return {
      loading,
      tableState,
      drawerState,
      searchInput,
      handleSearch,
      handleReset,
      handleDelete,
    }
  },
})