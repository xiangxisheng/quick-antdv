<template>
	<a-layout-content>
		<a-result status="404" title="404" subTitle="抱歉，您访问的页面不存在。" style="text-align: center;">
			<a-button type="primary" @click="$router.push('/')">返回首页</a-button>
		</a-result>
	</a-layout-content>
</template>

<script define>
const { LayoutContent, Result, Button } = antd;
const components = {
	ALayoutContent: LayoutContent,
	AResult: Result,
	AButton: Button,
};
</script>

<script setup>
return {};
</script>
