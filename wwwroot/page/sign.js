export default async (param) => {
  return {
    template: await (await fetch('./page/sign.htm')).text(),
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