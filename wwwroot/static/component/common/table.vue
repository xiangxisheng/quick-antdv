<template>
	<context-holder />
	<a-drawer :title="drawerState.title" :open="drawerState.open" :body-style="{ paddingBottom: '80px' }" :footer-style="{ textAlign: 'right' }"
		@close="$refs.drawerForm.clearValidate(); drawerState.open = false;" :maskClosable="drawerState.maskClosable">
		<a-form ref="drawerForm" :rules="drawerState.rules" :model="drawerState.model" name="basic" layout="vertical" @finish="drawerState.finish"
			@finishFailed="drawerState.finishFailed" autocomplete="off">
			<button type="submit" style="display: none;"></button>

			<a-form-item v-for="formItem of drawerState.formItems" :label="GTR(formItem.title)" :name="formItem.dataIndex">
				<div v-if="0"></div>
				<a-input v-else-if="formItem.form == 'input'" :disabled="formItem.disabled" :readonly="formItem.readonly"
					v-model:value="drawerState.model[formItem.dataIndex]" :placeholder="`${GTR(formItem.placeholder, { title: GTR(formItem.title) })}`"
					:addon-before="formItem.addonBefore" :addon-after="formItem.addonAfter" style="width: 100%" />
				<a-select v-else-if="formItem.form == 'select'" :disabled="formItem.disabled" v-model:value="drawerState.model[formItem.dataIndex]"
					:placeholder="GTR(formItem.placeholder, { title: GTR(formItem.title) })">
					<a-select-option v-for="option of formItem.options" :value="option.value">{{ GTR(option.title) }}</a-select-option>
				</a-select>
				<div v-else-if="formItem.form == 'date-picker'">
					<a-input v-if="formItem.readonly" :readonly="formItem.readonly" :value="drawerState.model[formItem.dataIndex]"></a-input>
					<a-date-picker v-else :disabled="formItem.disabled" v-model:value="formItem.value_date"
						@change="e => { drawerState.model[formItem.dataIndex] = e ? e.format(formItem.format) : null; }" style="width: 100%"
						:get-popup-container="trigger => trigger.parentElement" />
				</div>
				<a-textarea v-else-if="formItem.form == 'textarea'" :disabled="formItem.disabled" :readonly="formItem.readonly"
					v-model:value="drawerState.model[formItem.dataIndex]" :rows="4" :placeholder="GTR(formItem.placeholder, { title: GTR(formItem.title) })" />
			</a-form-item>

		</a-form>
		<template v-if="drawerState.action == 'add' || drawerState.action == 'edit'" #extra>
			<a-space>
				<span v-for="button of drawerState.buttons">
					<a-button v-if="button.type === 'primary'" :type="button.type" :loading="pageState.loading"
						@click="async () => { try { await $refs.drawerForm.validate(); drawerState.finish(); } catch (e) { drawerState.finishFailed(e); } }">{{
		GTR(button.title) }}</a-button>
					<a-button v-else @click="$refs.drawerForm.clearValidate(); drawerState.open = false;">{{ GTR(button.title) }}</a-button>
				</span>
			</a-space>
		</template>
	</a-drawer>
	<a-space wrap :size="20" style="margin: 10px;">
		<div v-for="button of pageState.buttons">
			<div v-if="0"></div>
			<a-popconfirm :title="GTR(button.popconfirm.title)" :ok-text="GTR(button.popconfirm.okText)" :cancel-text="GTR(button.popconfirm.cancelText)"
				:disabled="tableState.rowSelection.selectedRowKeys.length === 0" @confirm="pageState.handleButton(button)" v-else-if="button.popconfirm">
				<a-button type="primary" :danger="button.type === 'delete'" :loading="pageState.loading"
					:disabled="tableState.rowSelection.selectedRowKeys.length === 0">

					<template #icon>
						<span v-if="0"></span>
						<i v-else-if="button.type === 'delete'" class="bx bx-trash" style="font-size: 16px; vertical-align: -2px; margin-right: 4px;"></i>
						<i v-else-if="button.type === 'add'" class="bx bx-plus-circle" style="font-size: 16px; vertical-align: -2px; margin-right: 4px;"></i>
					</template>
					{{ GTR(button.title) }}
				</a-button>
			</a-popconfirm>
			<a-button type="primary" :danger="button.type === 'delete'" :loading="pageState.loading" @click="pageState.handleButton(button)" v-else>

				<template #icon>
					<span v-if="0"></span>
					<i v-else-if="button.type === 'delete'" class="bx bx-trash" style="font-size: 16px; vertical-align: -2px; margin-right: 4px;"></i>
					<i v-else-if="button.type === 'add'" class="bx bx-plus-circle" style="font-size: 16px; vertical-align: -2px; margin-right: 4px;"></i>
				</template>
				{{ GTR(button.title) }}
			</a-button>
		</div>
	</a-space>
	<a-table :dataSource="tableState.dataSource" :columns="tableState.columns" :pagination="tableState.pagination" :loading="pageState.loading"
		@change="tableState.change" :scroll="{ x: 500 }" :rowSelection="tableState.rowSelection" :rowKey="tableState.rowKey" :showSorterTooltip="false">

		<template #customFilterDropdown="{ setSelectedKeys, selectedKeys, confirm, clearFilters, column }">
			<a-space direction="vertical" :size="12" style="padding: 8px;">
				<div v-if="0"></div>
				<a-date-picker ref="searchInput" v-else-if="column.type === 'date'" v-model:value="column.search_dayjs[0]" :format="column.format"
					@change="e => setSelectedKeys(e ? [e.format(column.format)] : [])" style=" width: 188px;" />
				<a-input v-else ref="searchInput" :placeholder="`${GTR('table.search_placeholder', column)}`" :value="selectedKeys[0]" style="width: 188px;"
					@change="e => setSelectedKeys(e.target.value ? [e.target.value] : [])" @pressEnter="tableState.handleSearch(confirm)" />
				<a-space direction="horizontal">
					<a-button type="primary" size="small" style="width:90px;" @click="tableState.handleSearch(confirm)">
						<template #icon>
							<i class="bx bx-search" style="vertical-align: -2px; margin-right: 4px;"></i>
						</template>
						{{ GTR('table.search') }}
					</a-button>
					<a-button size="small" style="width:90px;" @click="tableState.handleReset(clearFilters)">
						{{ GTR('table.reset') }}
					</a-button>
				</a-space>
			</a-space>
		</template>

		<template #bodyCell="{ column, record }">
			<template v-if="column.operates">
				<a-space direction="horizontal">
					<span v-for="operate of column.operates">
						<span v-if="0"></span>
						<a-popconfirm :title="GTR(operate.popconfirm.title)" :ok-text="GTR(operate.popconfirm.okText)"
							:cancel-text="GTR(operate.popconfirm.cancelText)" @confirm="tableState.action(operate, record)" v-else-if="operate.popconfirm">
							<a>{{ GTR(operate.title) }}</a>
						</a-popconfirm>
						<a v-else v-on:click="tableState.action(operate, record)">{{ GTR(operate.title) }}</a>
					</span>
				</a-space>
			</template>
		</template>
	</a-table>
</template>

<script define>
const { backendApi, deepCloneObject, filterNullItem, tryParseJSON } = QADV;
const { array_set_recursive } = QADV;
const { ref, reactive, watch, onMounted, inject } = Vue;
const { Space, Table, Input, Button, Popconfirm, Drawer } = antd;
const { Form, FormItem, Row, Col, Textarea, DatePicker, Select, SelectOption } = antd;
const [messageApi, contextHolder] = antd.message.useMessage();
const { useRouter, useRoute } = VueRouter;
const components = {
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
};
</script>

<script setup>

// 1：路由及页面数据定义
const router = useRouter();
const route = useRoute();
const i18n = inject('i18n')();

i18n.$subscribe((mutation, state) => {
	ReloadTrans_dataSource();
});

const ReloadTrans_dataSource = async () => {
	// 翻译表头title
	for (const column of tableState.columns) {
		// 对当前显示的表头进行翻译
		if (column.title_tpl) {
			column.title = await i18n.fGetTransResult(column.title_tpl);
		}
	}
	for (const column of pageData.table.columns) {
		if (column.rules) {
			// 对全部有含rules的列进行翻译
			const rules = drawerState.rules[column.dataIndex] = deepCloneObject(column.rules);
			for (const rule of rules) {
				rule.message = await i18n.fGetTransResult(rule.message, column);
			}
		}
	}
	// 翻译表数据records
	for (const kRow in pageData.table.dataSource) {
		for (const kCol in pageData.table.dataSource[kRow]) {
			tableState.dataSource[kRow][kCol] = await i18n.fGetTransResult(pageData.table.dataSource[kRow][kCol]);
		}
	}
};

const pageData = {
	table: {
		columns: [],
		dataSource: {},
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
			// 每当drawer开启时都会触发翻译
			drawerState.title = await i18n.fGetTransResult(button.title);
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
		if (button.type === 'export') {
			const aoa = [];
			const col = [];
			const aDataIndex = [];
			for (const column of pageData.table.columns) {
				if (!column.dataIndex) {
					continue;
				}
				if (!column.width) {
					continue;
				}
				aDataIndex.push(column.dataIndex);
				col.push(await i18n.fGetTransResult(column.title));
			}
			aoa.push(col);
			for (const row of pageData.table.dataSource) {
				const col = [];
				for (const sDataIndex of aDataIndex) {
					col.push(await i18n.fGetTransResult(row[sDataIndex]));
				}
				aoa.push(col);
			}
			const jsonWorkSheet = XLSX.utils.aoa_to_sheet(aoa);
			const sheetName = await i18n.fGetTransResult(oTopRoute.label);
			const workBook = {
				SheetNames: [sheetName],
				Sheets: {
					[sheetName]: jsonWorkSheet,
				}
			};
			await XLSX.writeFile(workBook, `${sheetName}.xlsx`);
			return;
		}
		console.log('pageState.handleButton', button);
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
			// 显示页面统计，例如：当前显示从 1 到 18，共 329 条记录
			return i18n.fGetTransResult(pageData.table.pagination.showTotalTemplate, param);
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
		const path = route.name;
		if (pageState.loading) {
			return;
		}
		pageState.loading = true;
		const dataType = 'json';
		return await backendApi({ path, param, post, dataType, messageApi, router }).then((data) => {
			array_set_recursive(pageData, data);
			if (data.table) {
				tableReaderList(data.table);
			}
			return data;
		}).catch((ex) => {
			if (ex.text) {
				messageApi.error(ex.text);
				return {};
			}
			messageApi.error(ex.toString());
			return {};
		}).then((data) => {
			pageState.loading = false;
			return data;
		});
	};
	const tableReaderList = async (tableData) => {
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
				column.title_tpl = column.title;
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
			ReloadTrans_dataSource();
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
			// 在表格的操作栏中点查看或编辑时
			drawerState.open = true;
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
			// 每当drawer开启时都会触发翻译
			drawerState.title = await i18n.fGetTransResult(mAction.title);
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
					// 如果是点【查看】进去的，将所有输入框都设为只读
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
		Api.list();
		return;
		if (pageState.path === to.path) {
			Api.list();
		}
	}
);

function GTR(_formatpath, _param) {
	if (_formatpath === undefined) {
		return '';
	}
	return i18n.fGetTransResult(_formatpath, _param, i18n.locale);
};

// 7：返回页面
return {
	GTR,
	pageState,
	tableState,
	drawerState,
	searchInput,
}
</script>
