const { fetchDataByPathname } = firadio;
const { deepCloneObject } = firadio;
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
    const PageData = {
      columns: [],
    };
    const router = useRouter();
    const route = useRoute();
    const pageState = reactive({
      loading: false,
      handleButton: async (button) => {
        if (button.type === 'add') {
          drawerState.action = 'add';
          drawerState.title = button.title;
          drawerState.buttons = button.buttons;
          drawerState.open = true;
          drawerState.model = {};
          drawerState.formItems.length = 0;
          const formItems = deepCloneObject(PageData.columns);
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
    const jsonTryParse = (str) => {
      try {
        return JSON.parse(str);
      } catch (e) { }
      return {};
    };
    const fetchData_api = async (path, param) => {
      pageState.loading = true;
      const data = await fetchDataByPathname(path, param);
      pageState.loading = false;
      return data;
    };
    const fetchData_list = async () => {
      const query = route.query;
      const path = `api${route.path}.php`;
      const param = {};
      if (query.table_name) {
        param.table_name = query.table_name;
      }
      param.pagination = query.pagination;
      param.filters = query.filters;
      param.sorter = query.sorter;
      const data = await fetchData_api(path, param);
      PageData.columns = data.columns;
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
      drawerState.rules = {};
      for (const column of data.columns) {
        if (column.rules) {
          drawerState.rules[column.dataIndex] = column.rules;
        }
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
    tableState.action = async (mAction, record) => {
      const path = `api${route.path}.php`;
      const action = mAction.action;
      const param = { action };
      param[tableState.info.rowKey] = record[tableState.info.rowKey];
      const data = await fetchData_api(path, param);
      if (data.formModel) {
        drawerState.model = data.formModel;
        drawerState.action = action;
        drawerState.buttons = mAction.buttons;
        drawerState.title = mAction.title;
        drawerState.formItems.length = 0;
        const formItems = deepCloneObject(PageData.columns);
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
      }
    };
    fetchData_list();

    const drawerState = reactive({
      open: false,
      maskClosable: false,
      data: {},
      rules: {},
      model: {},
      formItems: [],
    });
    drawerState.finish = async () => {
      drawerState.open = false;
    }
    drawerState.finishFailed = (errorInfo) => {
      messageApi.error(errorInfo.errorFields[0].errors[0], 1);
    };
    watch(
      () => route.query,
      () => {
        fetchData_list();
      }
    );
    return {
      pageState,
      tableState,
      drawerState,
      searchInput,
    }
  },
})