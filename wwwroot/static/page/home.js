const { useRoute } = VueRouter;
export default async (oTopRoute) => ({
	template: await (await fetch(`${oTopRoute.setting.static_dir}/${oTopRoute.component}.htm`)).text(),
	setup() {
		const route = useRoute();
		const title = route.name;
		return { title };
	},
});
