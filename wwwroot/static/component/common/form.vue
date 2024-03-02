<template>
	<table v-if="pageState.loading && !pageState.data.items" width="100%" height="300">
		<tr>
			<td style="text-align: center; color: blue;">
				<h1>Loading...</h1>
			</td>
		</tr>
	</table>
	<span v-for="item in pageState.data['header']">
		<h1 v-if="item.type === 'title'" style="text-align: center; margin: 40px;">{{ GTR(item.title) }}</h1>
	</span>
	<a-form :model="formState" name="basic" :label-col="{ span: 8 }" :wrapper-col="{ span: 10 }" autocomplete="off" @finish="onFinish"
		@finishFailed="onFinishFailed" style="text-align: center; margin: 0 40px;">
		<a-form-item v-for="item in pageState.data['items']" :label="GTR(item.label)" :name="item.dataIndex" :rules="item.rules"
			:wrapper-col="item['wrapper-col']">
			<span v-if="0"></span>
			<a-input v-else-if="item.form === 'input'" v-model:value="formState[item.dataIndex]" :placeholder="GTR(item.placeholder)"
				:disabled="pageState.loading" />
			<a-input-password v-else-if="item.form === 'input-password'" v-model:value="formState[item.dataIndex]" :placeholder="GTR(item.placeholder)"
				:disabled="pageState.loading" />
			<a-checkbox v-else-if="item.form === 'checkbox'" v-model:checked="formState[item.dataIndex]" :disabled="pageState.loading">{{ GTR(item.title)
				}}</a-checkbox>
			<a-space wrap v-else-if="item.form === 'buttons'">
				<span v-for="bItem in item.buttons">
					<span v-if="0"></span>
					<a-button v-else-if="bItem.form === 'button' && bItem.type === 'link'" :type="bItem.type" v-on:click="router.push(bItem.link)">{{
		GTR(bItem.title) }}</a-button>
					<a-button v-else-if="bItem.form === 'button'" :type="bItem.type" :html-type="bItem['html-type']" :span="bItem.span"
						:loading="pageState.loading">{{ GTR(bItem.title) }}</a-button>
				</span>
			</a-space>
		</a-form-item>
	</a-form>
</template>

<script define>
const { backendApi, deepCloneObject, tryParseJSON } = QADV;
const { reactive, inject, onMounted } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Form, FormItem, Input, InputPassword, Checkbox, Space, Button } = antd;
const [messageApi, contextHolder] = antd.message.useMessage();
const components = {
	contextHolder,
	AForm: Form,
	AFormItem: FormItem,
	AInput: Input,
	AInputPassword: InputPassword,
	ACheckbox: Checkbox,
	ASpace: Space,
	AButton: Button,
};
</script>

<script setup>
const i18n = inject('i18n')();

function GTR(_formatpath, _param) {
	if (_formatpath === undefined) {
		return '';
	}
	return i18n.fGetTransResult(_formatpath, _param, i18n.locale);
};

const formState = reactive({
	username: '',
	password: '',
	remember: false,
});

const pageState = reactive({
	loading: false,
	data: {},
});

const Api = (() => {
	const apiAction = async (action, param, post) => {
		param.action = action;
		const path = `api${route.name}.php`;
		if (pageState.loading) {
			return;
		}
		pageState.loading = true;
		const dataType = 'json';
		return await backendApi({ path, param, post, dataType }).then((data) => {
			//array_set_recursive(pageData, data);
			if (data.message) {
				messageApi.open(data.message);
			}
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
	return ({
		init: async () => {
			const hide = antd.message.loading('Loading...', 0);
			const param = deepCloneObject(route.query);
			const data = await apiAction('init', param);
			hide();
			pageState.data = deepCloneObject(data);
		},
		submit: async (post) => {
			const param = deepCloneObject(route.query);
			await apiAction('create', param, post);
		},
	});
})();

const onFinish = async (values) => {
	console.log('Success:', values);
	await Api.submit(values);
};

const onFinishFailed = (errorInfo) => {
	console.log('Failed:', errorInfo);
};

const router = useRouter();
const route = useRoute();

onMounted(async () => {
	await Api.init();
});

return {
	GTR,
	router,
	route,
	formState,
	pageState,
	onFinish,
	onFinishFailed,
};
</script>
