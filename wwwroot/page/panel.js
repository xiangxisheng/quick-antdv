import { routes_filter } from 'firadio';
const { ref, reactive, watch, h } = Vue;
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
    const menuState = reactive({
      collapsed: false,
      openKeys: ref(['/console/data']),
      selectedKeys: ref([]),
      items: ref([]),
      handleClick: (e) => {
        router.push(e.key);
      },
    });
    menuState.selectedKeys = [route.name];
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
        mRet.label = mRoute.label;
        return mRet;
      })).children;
    })();
    watch(
      () => route.name,
      (v) => {
        if (v) {
          menuState.selectedKeys.value = [v];
        }
      }
    );
    return {
      menuState,
    };
  },
})