export default async (param) => {
  return {
    template: await (await fetch('./page/sign.htm')).text(),
    data() {
      return {
        title: 'xxx',
      }
    },
    async created() {
      this.title = this.$route.name;
    },
    methods: {

    },
  }
}