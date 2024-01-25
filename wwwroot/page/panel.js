const { routes_filter, stateStorage } = firadio;
const { reactive, watch, h, inject } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Layout, LayoutContent, LayoutFooter, LayoutSider, Menu, Breadcrumb, BreadcrumbItem } = antd;
export default async (oTopRoute) => ({
	template: await (await fetch('./page/panel.htm')).text(),
	components: {
		ALayout: Layout,
		ALayoutContent: LayoutContent,
		ALayoutFooter: LayoutFooter,
		ALayoutSider: LayoutSider,
		AMenu: Menu,
		ABreadcrumb: Breadcrumb,
		ABreadcrumbItem: BreadcrumbItem,
	},
	setup() {
		const router = useRouter();
		const route = useRoute();
		const i18n = inject('i18n')();
		i18n.$subscribe((mutation, state) => {
			menuState.ReloadTrans(menuState.items);
			breadcrumbState.ReloadTrans();
		});

		const oMenuState = stateStorage('menu');
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
	},
});
