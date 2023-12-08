export default async (oTopRoute) => {
  return {
    template: await (await fetch('./page/index.htm')).text(),
    data() {
      return {
        items: [],
        selectedKeys: [],
      }
    },
    created() {
      this.items.length = 0;
      for (const route of oTopRoute.children) {
        const item = {};
        item.key = route.path;
        item.label = route.label;
        this.items.push(item);
        console.log(item);
      }
      const path = this.$route.path;
      console.log(this.$route);
      const cur = '/' + path.split('/')[1];
      console.log(cur);
      this.selectedKeys.push(cur);
    },
    methods: {
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