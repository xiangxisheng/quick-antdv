const { fGetTransResult } = i18n;
const { ref, watch, onMounted } = Vue;
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
    const selectedKeys = ref();
    if (route.name) {
      const cur = route.name.split('/')[1];
      selectedKeys.value = [cur];
    }
    const handleClick = (e) => {
      router.push('/' + e.key);
    };
    onMounted(async () => {
      for (const mRoute of oTopRoute.children) {
        if (!mRoute.label) {
          continue;
        }
        const item = {};
        item.key = mRoute.name;
        item.label = await fGetTransResult(mRoute.label);
        items.value.push(item);
      }
    });
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