const { ref, watch } = Vue;
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
    const items = ref([]);
    for (const mRoute of oTopRoute.children) {
      if (!mRoute.label) {
        continue;
      }
      const item = {};
      item.key = mRoute.name;
      item.label = mRoute.label;
      items.value.push(item);
    }
    const selectedKeys = ref();
    if (route.name) {
      const cur = route.name.split('/')[1];
      selectedKeys.value = [cur];
    }
    const handleClick = (e) => {
      router.push('/' + e.key);
    };
    watch(
      () => route.name,
      (v) => {
        if (v) {
          const cur = v.split('/')[1];
          selectedKeys.value = [cur];
        }
      }
    );
    return {
      items,
      selectedKeys,
      handleClick,
    };
  },
})