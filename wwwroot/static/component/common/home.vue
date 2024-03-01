<template>
	<div>
		<router-link to="/console" class="nav-link">
			进入控制台
		</router-link>
		<br />
		<router-link to="/sign-in" class="nav-link">
			登录
		</router-link>

	</div>
</template>

<script define>
const { useRoute } = VueRouter;
const components = {

};
</script>

<script setup>
const route = useRoute();
const title = route.name;
return { title };
</script>
