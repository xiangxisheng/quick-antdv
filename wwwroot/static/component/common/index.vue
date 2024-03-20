<template>
	<context-holder />
	<a-layout style="min-height:100vh">
		<a-layout-header style="padding-inline: 0px;">

			<div style="float: left;">
				<router-link to="/">
					<img height="64" :src="`${setting.assets_dir}/img/logo.svg`" />
				</router-link>
			</div>

			<a-menu style="float: left;" v-model:selectedKeys="menuState.selectedKeys" mode="horizontal" :items="menuState.items" theme="dark"
				@click="menuState.handleClick"></a-menu>

			<div style="float: right; padding-inline: 10px; color: #999;">
				<a-dropdown :trigger="['click']">
					<a class="ant-dropdown-link" @click.prevent>
						{{ localeState.current }}
						<i class='bx bx-chevron-down'></i>
					</a>
					<template #overlay>
						<a-menu>
							<a-menu-item v-for="locale in localeState.get()" :key="locale.name">
								<a @click="localeState.set(locale.name)">{{ locale.title }}</a>
							</a-menu-item>
						</a-menu>
					</template>
				</a-dropdown>
			</div>

			<div style="float: right; padding-inline: 10px; color: #999;">
				<a-dropdown :trigger="['click']">
					<a class="ant-dropdown-link" @click.prevent>
						admin
						<i class='bx bx-chevron-down'></i>
					</a>
					<template #overlay>
						<a-menu>
							<a-menu-item @click="user_logout()">{{ GTR('user.logout') }}</a-menu-item>
						</a-menu>
					</template>
				</a-dropdown>
			</div>

		</a-layout-header>
		<a-layout-content>
			<router-view></router-view>
		</a-layout-content>
	</a-layout>
</template>

<script define>
const { backendApi } = QADV;
const { reactive, watch, onMounted, inject } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Layout, LayoutHeader, LayoutContent, Menu, MenuItem, Dropdown } = antd;
const [messageApi, contextHolder] = antd.message.useMessage();
const components = {
	ALayout: Layout,
	ALayoutContent: LayoutContent,
	ALayoutHeader: LayoutHeader,
	AMenu: Menu,
	AMenuItem: MenuItem,
	ADropdown: Dropdown,
	contextHolder,
};
</script>

<script setup>
const setting = oTopRoute.setting;
const router = useRouter();
const route = useRoute();
const i18n = inject('i18n')();
i18n.$subscribe((mutation, state) => {
	ReloadTrans();
});

const localeState = reactive({
	current: '',
	set: async (locale) => {
		await i18n.fSetCurrentLocale(locale);
	},
	get: () => {
		return i18n.mConfLocale;
	},
});

const menuState = reactive({
	items: [],
	selectedKeys: [],
	handleClick: (e) => {
		router.push('/' + e.key);
	},
});

if (route.name) {
	const cur = route.name.split('/')[1];
	menuState.selectedKeys = [cur];
}

const ReloadTrans = async () => {
	localeState.current = i18n.mConfLocale[i18n.locale].title;
	document.title = await i18n.fGetTransResult('site.title');
	for (const item of menuState.items) {
		item.label = await i18n.fGetTransResult(item.label_tpl);
	}
};

onMounted(async () => {
	const user_roles = ['sysadmin', 'user', 'public'];
	for (const mRoute of oTopRoute.children) {
		if (!mRoute.label) {
			continue;
		}
		const item = {};
		item.key = mRoute.name;
		item.label_tpl = mRoute.label;
		if (!mRoute.role || user_roles.indexOf(mRoute.role) !== -1) {
			menuState.items.push(item);
		}
	}
	await i18n.fLoadData();
});

watch(
	() => route.name,
	(v) => {
		if (v) {
			const cur = v.split('/')[1];
			menuState.selectedKeys = [cur];
		}
	}
);

async function user_logout() {
	const path = "/logout";
	const res = await backendApi({ path, messageApi, router });
	console.log(res);
};

function GTR(_formatpath, _param) {
	if (_formatpath === undefined) {
		return '';
	}
	return i18n.fGetTransResult(_formatpath, _param, i18n.locale);
};

return {
	user_logout,
	GTR,
	setting,
	localeState,
	menuState,
};
</script>
