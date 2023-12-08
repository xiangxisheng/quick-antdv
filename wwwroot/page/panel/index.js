export default async (param) => {
  return {
    template: await (await fetch('./page/panel/index.htm')).text(),
    data() {
      return {
        xxx: 'asdfasdfasdf',
      }
    },
    async created() {
      this.$router.push({ query: { date: new Date() } });
      this.xxx = this.$route.path + ',' + JSON.stringify(this.$route.query);
    },
    methods: {

    },
  }
}
