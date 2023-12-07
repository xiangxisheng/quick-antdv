import { routes_filter } from 'firadio';
export default async (oTopRoute) => {
  return {
    template: await (await fetch('./page/panel.htm')).text(),
    data() {
      return {
        items: [],
        openKeys: [],
        selectedKeys: [],
      }
    },
    async created() {
      this.openKeys.push('/panel/data');
      this.selectedKeys.push(this.$route.path);
      const routes1 = await routes_filter(oTopRoute, async (sParent, mRoute) => {
        const mRet = {};
        mRet.key = `${sParent}/${mRoute.name}`;
        mRet.label = mRoute.label;
        return mRet;
      });
      this.items = routes1.children;
    },
    methods: {
      handleClick(e) {
        this.$router.push(e.key);
      }
    },
  }
}
