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
      this.openKeys.push('/console/data');
      this.selectedKeys.push(this.$route.path);
      this.items = (await routes_filter(oTopRoute, async (sParent, mRoute) => {
        const mRet = {};
        mRet.key = `${sParent}/${mRoute.name}`;
        mRet.label = mRoute.label;
        return mRet;
      })).children;
    },
    methods: {
      handleClick(e) {
        this.$router.push(e.key);
      }
    },
  }
}
