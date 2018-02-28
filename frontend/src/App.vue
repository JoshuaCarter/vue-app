<template>
	<div>
		<b-container>
			<NavBar></NavBar>
			<PersonPage></PersonPage>
		</b-container>
	</div>
</template>

<script>
import NavBar from './components/NavBar.vue'
import PersonPage from './components/PersonPage.vue'

export default {
	components: {
		NavBar,
		PersonPage
	}
}
</script>

<style>
#app {
	font-family: 'Avenir', Helvetica, Arial, sans-serif;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
	color: #2c3e50;
}

body {
	margin: 0;
	padding: 0;
	font-family: sans-serif;
	background-color: #6c757d;
	height: 100%;
}
</style>
