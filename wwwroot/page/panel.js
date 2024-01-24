const { routes_filter, stateStorage } = firadio;
const { ref, reactive, watch, h, inject } = Vue;
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
      ReloadTrans();
    });

    const oMenuState = stateStorage('menu');
    const menuState = reactive({
      collapsed: false,
      openKeys: ref(['/console/data']),
      selectedKeys: [],
      items: [],
      handleClick: (e) => {
        router.push(e.key);
      },
      fIsMobile: () => {
        return window.innerWidth < 700;
      },
    });
    if (oMenuState.has('collapsed')) {
      menuState.collapsed = oMenuState.get('collapsed');
    } else {
      menuState.collapsed = menuState.fIsMobile();
    }
    window.onresize = () => {
      menuState.collapsed = menuState.fIsMobile();
    };
    menuState.selectedKeys = [route.name];

    const ReloadTrans = async () => {
      for (const item of menuState.items) {
        item.label = await i18n.fGetTransResult(item.label_tpl);
      }
    };

    (async () => {
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
      ReloadTrans();
    })();

    watch(
      () => route.name,
      (v) => {
        if (v) {
          menuState.selectedKeys = [v];
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
    };
  },
});
