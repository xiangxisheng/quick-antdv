<template>
	<a-layout style="height: 100%">
		<a-layout-sider v-model:collapsed="menuState.collapsed" style="min-height: calc(100vh - 64px);" :collapsedWidth="40" collapsible>
			<a-menu v-model:openKeys="menuState.openKeys" v-model:selectedKeys="menuState.selectedKeys" mode="inline" theme="dark"
				:inline-collapsed="menuState.collapsed" :items="menuState.items" @click="menuState.handleClick"></a-menu>
		</a-layout-sider>
		<a-layout>
			<a-layout-content style="margin: 0 8px">
				<a-breadcrumb style="margin: 6px 0">
					<a-breadcrumb-item v-for="item of breadcrumbState.items">{{ item.label }}</a-breadcrumb-item>
				</a-breadcrumb>
				<div>
					<router-view></router-view>
				</div>
			</a-layout-content>
			<a-layout-footer style="text-align: center">
				Ant Design ©2018 Created by Ant UED
			</a-layout-footer>
		</a-layout>
	</a-layout>
</template>

<script define>
const { routes_filter, stateStorage } = QADV;
const { reactive, watch, h, inject } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Layout, LayoutContent, LayoutFooter, LayoutSider, Menu, Breadcrumb, BreadcrumbItem } = antd;
const components = {
	ALayout: Layout,
	ALayoutContent: LayoutContent,
	ALayoutFooter: LayoutFooter,
	ALayoutSider: LayoutSider,
	AMenu: Menu,
	ABreadcrumb: Breadcrumb,
	ABreadcrumbItem: BreadcrumbItem,
};
</script>

<script setup>

const router = useRouter();
const route = useRoute();
const i18n = inject('i18n')();
i18n.$subscribe((mutation, state) => {
	menuState.ReloadTrans(menuState.items);
	breadcrumbState.ReloadTrans();
});

const oMenuState = stateStorage('menu');
const user_roles = ['sysadmin', 'user', 'public'];
const menuState = reactive({
	collapsed: false,
	openKeys: [],
	selectedKeys: [],
	items: [],
	handleClick: (e) => {
		router.push(e.key);
	},
	fIsMobile: () => {
		return window.innerWidth < 700;
	},
	ReloadTrans(items) {
		for (const item of items) {
			item.label = i18n.fGetTransResult(item.label_tpl);
			if (item.children) {
				menuState.ReloadTrans(item.children);
			}
		}
	},
	async init() {
		if (oMenuState.has('collapsed')) {
			menuState.collapsed = oMenuState.get('collapsed');
		} else {
			menuState.collapsed = menuState.fIsMobile();
		}
		window.onresize = () => {
			menuState.collapsed = menuState.fIsMobile();
		};
		menuState.selectedKeys = [route.name];
		menuState.items = (await routes_filter(oTopRoute, async (sParent, mRoute) => {
			if (mRoute.role && user_roles.indexOf(mRoute.role) === -1) {
				return;
			}
			const mRet = {};
			mRet.key = `${sParent}/${mRoute.name}`;
			mRet.icon = () => h({
				template: '<i class="bx bx-radio-circle" style="font-size: 16px; vertical-align: -2px; margin-right: 4px;"></i>',
				setup() {
					return {};
				}
			});
			mRet.label_tpl = mRoute.label;
			return mRet;
		})).children;
		for (const match of route.matched) {
			if (!match.name) {
				continue;
			}
			if (!match.children) {
				continue;
			}
			if (match.children.length === 0) {
				continue;
			}
			// 当加载页面时，自动展开菜单所在父项
			menuState.openKeys.push(match.name);
		}
		menuState.ReloadTrans(menuState.items);
	},
});

const breadcrumbState = reactive({
	items: [],
	ReloadTrans() {
		breadcrumbState.items.length = 0;
		for (const match of route.matched) {
			if (!match.meta.hasOwnProperty('label')) {
				continue;
			}
			breadcrumbState.items.push({
				label: i18n.fGetTransResult(match.meta.label),
			});
		}
	},
	init() {
		breadcrumbState.ReloadTrans();
	},
});

menuState.init();
breadcrumbState.init();

watch(
	() => route.name,
	(v) => {
		if (v) {
			menuState.selectedKeys = [v];
			breadcrumbState.ReloadTrans();
		}
	}
);

watch(
	() => menuState.collapsed,
	(v) => {
		oMenuState.set('collapsed', v);
		oMenuState.save();
	}
);

return {
	menuState,
	breadcrumbState,
};
</script>
