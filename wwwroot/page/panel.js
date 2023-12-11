import { routes_filter } from 'firadio';
const { ref, watch } = Vue;
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
    const items = ref([]);
    const openKeys = ref(['/console/data']);
    const selectedKeys = ref([]);
    const collapsed = ref(false);
    selectedKeys.value = [route.name];
    (async () => {
      items.value = (await routes_filter(oTopRoute, async (sParent, mRoute) => {
        const mRet = {};
        mRet.key = `${sParent}/${mRoute.name}`;
        mRet.label = mRoute.label;
        return mRet;
      })).children;
    })();
    const handleClick = (e) => {
      router.push(e.key);
    };
    watch(
      () => route.name,
      (v) => {
        if (v) {
          selectedKeys.value = [v];
        }
      }
    );
    return {
      items,
      openKeys,
      selectedKeys,
      collapsed,
      handleClick,
    };
  },
})