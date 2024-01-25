const { reactive, watch, onMounted, inject } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Layout, LayoutHeader, LayoutContent, Menu, MenuItem, Dropdown } = antd;

export default async (oTopRoute) => ({
	template: await (await fetch('./page/index.htm')).text(),
	components: {
		ALayout: Layout,
		ALayoutContent: LayoutContent,
		ALayoutHeader: LayoutHeader,
		AMenu: Menu,
		AMenuItem: MenuItem,
		ADropdown: Dropdown,
	},
	setup() {
		const router = useRouter();
		const route = useRoute();
		const i18n = inject('i18n')();

		i18n.$subscribe((mutation, state) => {
			ReloadTrans();
		});

		const localeState = reactive({
			set: async (locale) => {
				i18n.fSetCurrentLocale(locale);
			},
			get: () => {
				return i18n.mConfLocale;
			},
			getCurrent: () => {
				return i18n.mConfLocale[i18n.locale].title;
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
			document.title = await i18n.fGetTransResult('site.title');
			for (const item of menuState.items) {
				item.label = await i18n.fGetTransResult(item.label_tpl);
			}
		};

		onMounted(async () => {
			for (const mRoute of oTopRoute.children) {
				if (!mRoute.label) {
					continue;
				}
				const item = {};
				item.key = mRoute.name;
				item.label_tpl = mRoute.label;
				menuState.items.push(item);
			}
			ReloadTrans();
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

		return {
			localeState,
			menuState,
		};
	},
});
