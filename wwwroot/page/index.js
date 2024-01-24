const { fGetTransResult, fSetCurrentLocale } = i18n;
const { reactive, watch, onMounted } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Layout, LayoutHeader, LayoutContent, Menu } = antd;
export default async (oTopRoute) => ({
  template: await (await fetch('./page/index.htm')).text(),
  components: {
    ALayout: Layout,
    ALayoutContent: LayoutContent,
    ALayoutHeader: LayoutHeader,
    AMenu: Menu,
  },
  setup() {
    const router = useRouter();
    const route = useRoute();

    const localeState = reactive({
      set: async (locale) => {
        fSetCurrentLocale(locale);
        ReloadTrans();
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
      document.title = await fGetTransResult('site.title');
      for (const item of menuState.items) {
        item.label = await fGetTransResult(item.label_tpl);
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
