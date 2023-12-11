const { useRoute } = VueRouter;
export default async () => ({
  template: '<router-view></router-view>',
  setup() {
    const route = useRoute();
    const title = route.name;
    return { title };
  },
})