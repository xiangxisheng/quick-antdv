import { routes_filter } from 'firadio';
export default async (oTopRoute) => {
  return {
    template: await (await fetch('./page/index.htm')).text(),
    data() {
      return {
        items: [],
        selectedKeys: [],
      }
    },
    watch: {
      $route(to) {
        const cur = to.name.split('/')[1];
        this.selectedKeys = [cur];
      }
    },
    async created() {
      this.items.length = 0;
      for (const mRoute of oTopRoute.children) {
        const item = {};
        item.key = mRoute.name;
        item.label = mRoute.label;
        this.items.push(item);
      }
      // this.items = (await routes_filter(oTopRoute, async (sParent, mRoute) => {
      //   const item = {};
      //   item.key = `${sParent}/${mRoute.name}`;
      //   item.isDir = mRoute.children ? true : false;
      //   item.label = mRoute.label;
      //   return item;
      // })).children;
      const cur = this.$route.name.split('/')[1];
      this.selectedKeys = [cur];
    },
    methods: {
      handleClick(e) {
        this.$router.push('/' + e.key);
      },
      goToDashboard() {
        if (isAuthenticated) {
          this.$router.push('/dashboard')
        } else {
          this.$router.push('/login')
        }
      },
    },
  }
}