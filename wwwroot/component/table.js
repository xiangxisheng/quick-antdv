const { backendApi, deepCloneObject, filterNullItem, tryParseJSON } = firadio;
const { array_set_recursive } = firadio;
const { fGetTransResult } = i18n;
const { ref, reactive, watch, onMounted } = Vue;
const { Space, Table, Input, Button, Popconfirm, Drawer } = antd;
const { Form, FormItem, Row, Col, Textarea, DatePicker, Select, SelectOption } = antd;
const [messageApi, contextHolder] = antd.message.useMessage();
const { useRouter, useRoute } = VueRouter;

export default async () => ({
  template: await (await fetch('./component/table.htm')).text(),
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

    // 1：路由及页面数据定义
    const router = useRouter();
    const route = useRoute();
    const pageData = {
      table: {
        columns: [],
      },
    };

    // 2：页面状态
    const pageState = reactive({
      loading: false,
      path: route.path,
      buttons: [],
      handleButton: async (button) => {
        if (button.type === 'add') {
          drawerState.action = 'add';
          drawerState.title = await fGetTransResult(button.title);
          drawerState.buttons = button.buttons;
          drawerState.open = true;
          drawerState.model = {};
          drawerState.formItems.length = 0;
          const formItems = deepCloneObject(pageData.table.columns);
          for (const formItem of formItems) {
            if (!formItem.form) {
              continue;
            }
            if (formItem.hasOwnProperty('default')) {
              drawerState.model[formItem.dataIndex] = formItem.default;
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
          Api.delete(tableState.rowSelection.selectedRowKeys);
          return;
        }
      },
    });

    // 3：抽屉状态
    const drawerState = reactive({
      operId: '',
      open: false,
      maskClosable: false,
      data: {},
      rules: {},
      model: {},
      formItems: [],
      finish: async () => {
        if (drawerState.action === 'add') {
          await Api.create(drawerState.model);
        }
        if (drawerState.action === 'edit') {
          await Api.update(drawerState.operId, drawerState.model);
        }
        drawerState.open = false;
      },
      finishFailed: (errorInfo) => {
        messageApi.error(errorInfo.errorFields[0].errors[0], 1);
      },
    });

    // 4：表格状态
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
          const param = { total, begin: range[0], end: range[1] };
          return fGetTransResult(pageData.table.pagination.showTotalTemplate, param);
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
      change: async (pagination, filters, sorter) => {
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
      },
      action: async (mAction, record) => {
        const operId = record[pageData.table.rowKey];
        const action = mAction.action;
        if (action === 'delete') {
          await Api.delete(operId);
          return;
        }
        await Api.view(operId, mAction);
      },
    });

    // 5：API相关
    const searchInput = ref('');
    const Api = (() => {
      const apiAction = async (action, param, post) => {
        param.action = action;
        const path = `api${route.path}.php`;
        if (pageState.loading) {
          return;
        }
        pageState.loading = true;
        const dataType = 'json';
        const data = await backendApi({ path, param, post, dataType });
        array_set_recursive(pageData, data);
        if (data.message) {
          messageApi.open(data.message);
        }
        if (data.table) {
          tableReaderList(data.table);
        }
        pageState.loading = false;
        return data;
      };
      const tableReaderList = (tableData) => {
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
        if (tableData.columns) {
          drawerState.rules = {};
          tableState.columns.length = 0;
          for (const column of tableData.columns) {
            column.title = fGetTransResult(column.title);
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
        }
        const query = route.query;
        const oQueryFilters = tryParseJSON(query.filters);
        const oQuerySorter = tryParseJSON(query.sorter);
        if (oQueryFilters && oQuerySorter) {
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
        }
        if (tableData.pagination) {
          array_set_recursive(tableState.pagination, tableData.pagination);
        }
        if (tableData.dataSource) {
          tableState.dataSource = tableData.dataSource;
        }
      }
      return ({
        init: async () => {
          const param = deepCloneObject(route.query);
          const data = await apiAction('init', param);
          if (data.buttons) {
            pageState.buttons = data.buttons;
          }
        },
        list: async () => {
          const param = deepCloneObject(route.query);
          await apiAction('list', param);
        },
        view: async (id, mAction) => {
          const action = mAction.action;
          const data = await apiAction('view', { id });
          if (!data) {
            return;
          }
          if (!data.formModel) {
            return;
          }
          drawerState.operId = id;
          drawerState.model = data.formModel;
          drawerState.action = action;
          drawerState.buttons = mAction.buttons;
          drawerState.title = await fGetTransResult(mAction.title);
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
        },
        delete: async (ids) => {
          const param = deepCloneObject(route.query);
          param.ids = ids;
          await apiAction('delete', param);
        },
        create: async (post) => {
          const param = deepCloneObject(route.query);
          await apiAction('create', param, post);
        },
        update: async (id, post) => {
          const param = deepCloneObject(route.query);
          param.id = id;
          await apiAction('update', param, post);
        },
      });
    })();

    // 6：Vue事件处理
    onMounted(async () => {
      await Api.init();
    });
    watch(
      route,
      async (to) => {
        if (pageState.path === to.path) {
          Api.list();
        }
      }
    );

    function GTR(_formatpath, _param) {
      return fGetTransResult(_formatpath, _param);
    };

    // 7：返回页面
    return {
      GTR,
      pageState,
      tableState,
      drawerState,
      searchInput,
    }
  },
});