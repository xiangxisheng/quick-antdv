<template>
	<h1 style="text-align: center; margin: 40px;">{{ route.name === '/sign-up' ? GTR('sign.register') : GTR('sign.login') }}</h1>

	<a-form :model="formState" name="basic" :label-col="{ span: 8 }" :wrapper-col="{ span: 10 }" autocomplete="off" @finish="onFinish"
		@finishFailed="onFinishFailed" style="text-align: center; margin: 0 40px;">
		<a-form-item :label="GTR('sign.username')" name="username"
			:rules="[{ required: true, message: GTR('table.please_enter', { title: GTR('sign.username') }) }]">
			<a-input v-model:value="formState.username" />
		</a-form-item>

		<a-form-item :label="GTR('sign.password')" name="password"
			:rules="[{ required: true, message: GTR('table.please_enter', { title: GTR('sign.password') }) }]">
			<a-input-password v-model:value="formState.password" />
		</a-form-item>

		<a-form-item v-if="route.name === '/sign-up'" :label="GTR('sign.confirmPassword')" name="password_confirm"
			:rules="[{ required: true, message: 'Please confirm your password!' }]">
			<a-input-password v-model:value="formState.password_confirm" />
		</a-form-item>

		<a-form-item name="remember" :wrapper-col="{ span: 24 }">
			<a-checkbox v-model:checked="formState.remember">Remember me</a-checkbox>
		</a-form-item>

		<a-form-item :wrapper-col="{ span: 24 }">
			<a-space wrap>
				<a-button type="link" v-on:click="router.push('/sign-up')" v-if="route.name === '/sign-in'">{{ GTR('sign.register') }}</a-button>
				<a-button type="link" v-on:click="router.push('/sign-in')" v-if="route.name === '/sign-up'">{{ GTR('sign.login') }}</a-button>
				<a-button type="primary" html-type="submit" span="8">{{ GTR('sign.submit') }}</a-button>
			</a-space>

		</a-form-item>
	</a-form>
</template>

<script define>
const { reactive, inject } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Form, FormItem, Input, InputPassword, Checkbox, Space, Button } = antd;
const components = {
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
const onFinish = (values) => {
	console.log('Success:', values);
};

const onFinishFailed = (errorInfo) => {
	console.log('Failed:', errorInfo);
};

const router = useRouter();
const route = useRoute();

return {
	GTR,
	router,
	route,
	formState,
	onFinish,
	onFinishFailed,
};
</script>
