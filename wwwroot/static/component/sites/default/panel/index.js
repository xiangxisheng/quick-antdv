const { computed } = Vue;
const { useRouter, useRoute } = VueRouter;
export default async () => ({
	template: await (await fetch(`${oTopRoute.setting.static_dir}/${oTopRoute.component}.htm`)).text(),
	setup() {
		const router = useRouter();
		const route = useRoute();
		router.push({ query: { date: new Date() } });
		const xxx = computed(() => (route.path + ',' + JSON.stringify(route.query)));
		return { xxx };
	},
});
