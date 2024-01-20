const { fetchDataByPathname, deepCloneObject, filterNullItem, tryParseJSON } = firadio;
const { array_set_recursive } = firadio;
const { ref, reactive, watch, onMounted } = Vue;
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
    const pageData = {
      table: {
        columns: [],
      },
    };
    const pageState = reactive({
      loading: false,
      path: route.path,
      buttons: [],
      handleButton: async (button) => {
        if (button.type === 'add') {
          drawerState.action = 'add';
          drawerState.title = button.title;
          drawerState.buttons = button.buttons;
          drawerState.open = true;
          drawerState.model = {};
          drawerState.formItems.length = 0;
          const formItems = deepCloneObject(pageData.table.columns);
          for (const formItem of formItems) {
            if (!formItem.form) {
              continue;
            }
            if (formItem.disabled) {
              continue;
            }
            if (formItem.readonly) {
              continue;
            }
            drawerState.formItems.push(formItem);
          }
          return;
        }
        if (button.type === 'delete') {
          messageApi.info(JSON.stringify(tableState.rowSelection.selectedRowKeys));
          return;
        }
      },
    });
    const tableState = reactive({
      rowKey: 'id',
      columns: [],
      dataSource: [],
      rowSelection: null,
      pagination: {
        total: 0,
        current: 1,
        pageSize: 20,
        showTotal: (total, range) => {
          if (!pageData.table) {
            return;
          }
          if (!pageData.table.pagination) {
            return;
          }
          var str = pageData.table.pagination.showTotalTemplate;
          if (!str) {
            return;
          }
          str = str.replaceAll('{total}', total);
          str = str.replaceAll('{begin}', range[0]);
          str = str.replaceAll('{end}', range[1]);
          return str;
        },
        pageSizeOptions: ['10', '20', '30', '50', '100', '200'],
      },
      handleSearch: (confirm) => {
        confirm();
      },
      handleReset: (clearFilters) => {
        clearFilters({
          confirm: true,
        });
      },
    });
    const searchInput = ref('');
    const fetchData_api = async (path, param) => {
      if (pageState.loading) {
        return;
      }
      pageState.loading = true;
      const data = await fetchDataByPathname(path, param);
      pageState.loading = false;
      return data;
    };
    const fetchData_init = async () => {
      const path = `api${route.path}.php`;
      const param = deepCloneObject(route.query);
      param.action = 'init';
      const data = await fetchData_api(path, param);
      const tableData = pageData.table = data.table;
      if (tableData.pagination) {
        array_set_recursive(tableState.pagination, tableData.pagination);
      }
      if (tableData.rowKey) {
        tableState.rowKey = tableData.rowKey;
      }
      if (tableData.rowSelection) {
        tableState.rowSelection = {
          selectedRowKeys: [],
        };
        tableState.rowSelection.onChange = (selectedRowKeys) => {
          tableState.rowSelection.selectedRowKeys = selectedRowKeys;
        };
      }
      tableState.columns.length = 0;
      if (data.buttons) {
        pageState.buttons = data.buttons;
      }
      drawerState.rules = {};
      for (const column of tableData.columns) {
        if (column.rules) {
          drawerState.rules[column.dataIndex] = column.rules;
        }
        if (!column.width) {
          continue;
        }
        if (column.type === 'sequence') {
          column.customRender = (o) => {
            const tableData = pageData.table;
            var iRet = o.index + 1;
            iRet += (tableData.pagination.current - 1) * tableData.pagination.pageSize
            return iRet;
          }
        }
        column.search_dayjs = [];
        column.customFilterDropdown = column.sql_where ? true : false;
        column.onFilterDropdownOpenChange = visible => {
          if (visible) {
            setTimeout(() => {
              searchInput.value.focus();
            }, 100);
          }
        };
        tableState.columns.push(column);
      }
      tableReaderList(tableData);
    }
    const tableReaderList = (tableData) => {
      const query = route.query;
      const oQueryFilters = tryParseJSON(query.filters);
      const oQuerySorter = tryParseJSON(query.sorter);
      for (const column of tableState.columns) {
        if (oQueryFilters[column.dataIndex]) {
          column.filteredValue = oQueryFilters[column.dataIndex];
          if (column.type === 'date') {
            for (var i = 0; i < column.filteredValue.length; i++) {
              column.search_dayjs[i] = dayjs(column.filteredValue[i], column.format);
            }
          }
        } else {
          delete column.filteredValue;
          column.search_dayjs = [];
        }
        if (oQuerySorter['field'] === column.dataIndex && oQuerySorter['order']) {
          column.sortOrder = oQuerySorter['order'];
        } else {
          delete column.sortOrder;
        }
      }
      if (tableData.pagination) {
        array_set_recursive(tableState.pagination, tableData.pagination);
      }
      if (tableData.dataSource) {
        tableState.dataSource = tableData.dataSource;
      }
    }
    const fetchData_list = async () => {
      const path = `api${route.path}.php`;
      const param = deepCloneObject(route.query);
      param.action = 'list';
      const data = await fetchData_api(path, param);
      array_set_recursive(pageData, data);
      tableReaderList(pageData.table);
    };
    tableState.change = async (pagination, filters, sorter) => {
      const query = {};
      query.pagination = JSON.stringify({ current: pagination.current, pageSize: pagination.pageSize });
      filterNullItem(filters);
      if (Object.keys(filters).length) {
        query.filters = JSON.stringify(filters);
      }
      if (sorter.order) {
        query.sorter = JSON.stringify({ order: sorter.order, field: sorter.field });
      }
      router.push({ query });
    }
    tableState.action = async (mAction, record) => {
      const path = `api${route.path}.php`;
      const action = mAction.action;
      const param = { action };
      param[pageData.table.rowKey] = record[pageData.table.rowKey];
      const data = await fetchData_api(path, param);
      if (!data) {
        return;
      }
      if (!data.formModel) {
        return;
      }
      drawerState.model = data.formModel;
      drawerState.action = action;
      drawerState.buttons = mAction.buttons;
      drawerState.title = mAction.title;
      drawerState.formItems.length = 0;
      const formItems = deepCloneObject(pageData.table.columns);
      for (const formItem of formItems) {
        if (!formItem.form) {
          continue;
        }
        if (!data.formModel.hasOwnProperty(formItem.dataIndex)) {
          continue;
        }
        if (action !== 'edit') {
          formItem.readonly = true;
        }
        const formValue = data.formModel[formItem.dataIndex];
        if (formItem.form === 'date-picker') {
          formItem.value_date = formValue ? dayjs(formValue, formItem.format) : null;
        }
        if (formItem.form === 'select') {
          if (formItem.readonly) {
            const options = [];
            for (const option of formItem.options) {
              if (option.value === formValue) {
                options.push(option);
              }
            }
            formItem.options = options;
          }
        }
        drawerState.formItems.push(formItem);
      }
      drawerState.maskClosable = action === 'view';
      drawerState.open = true;
    };

    const drawerState = reactive({
      open: false,
      maskClosable: false,
      data: {},
      rules: {},
      model: {},
      formItems: [],
      finish: async () => {
        drawerState.open = false;
      },
      finishFailed: (errorInfo) => {
        messageApi.error(errorInfo.errorFields[0].errors[0], 1);
      },
    });

    onMounted(async () => {
      await fetchData_init();
    });

    watch(
      route,
      async (to) => {
        if (pageState.path === to.path) {
          fetchData_list();
        }
      }
    );

    return {
      pageState,
      tableState,
      drawerState,
      searchInput,
    }
  },
});