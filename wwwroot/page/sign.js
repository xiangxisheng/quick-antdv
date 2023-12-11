const { useRoute } = VueRouter;
export default async () => ({
  template: await (await fetch('./page/sign.htm')).text(),
  setup() {
    const route = useRoute();
    const title = route.name;
    return { title };
  },
})