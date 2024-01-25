const { useRoute } = VueRouter;
const { LayoutContent, Result, Button } = antd;
export default async () => ({
	template: await (await fetch('./component/404.htm')).text(),
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
