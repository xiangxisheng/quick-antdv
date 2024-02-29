const { useRoute } = VueRouter;
const { LayoutContent, Result, Button } = antd;
export default async () => ({
	template: await (await fetch(`${oTopRoute.config.static_dir}/${oTopRoute.component}.htm`)).text(),
	components: {
		ALayoutContent: LayoutContent,
		AResult: Result,
		AButton: Button,
	},
	setup() {
		const route = useRoute();
		const title = route.name;
		return { title };
	},
});
