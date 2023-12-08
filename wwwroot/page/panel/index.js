export default async (param) => {
  return {
    template: await (await fetch('./page/panel/index.htm')).text(),
    data() {
      return {
        xxx: 'asdfasdfasdf',
      }
    },
    async created() {
      this.$route.params['aa'] = 'asdf';
      this.xxx = this.$route.path + ',' + JSON.stringify(this.$route.params);
    },
    methods: {

    },
  }
}
