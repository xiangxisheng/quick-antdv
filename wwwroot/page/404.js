export default async (param) => {
  return {
    template: await (await fetch('./page/404.htm')).text(),
    data() {
      return {

      }
    },
    async created() {

    },
    methods: {

    },
  }
}