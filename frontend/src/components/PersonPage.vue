<template>
	<div>
		<!-- Loading animation -->
		<div v-if="this.people.length == 0" class="d-flex justify-content-center">
			<div class="loader"></div>
		</div>

		<!-- People -->
		<Person v-for="p in people" :data="p"></Person>

		<!-- Pagination -->
		<b-pagination v-if="this.people.length > 0" size="md" align="center" :total-rows="total_people" :per-page="people_per_page" v-model="page_id" v-bind:limit="limit" v-on:change="onPageChange"></b-pagination>
	</div>
</template>

<script>
import SWAPI from '../api/SWAPI.js'
import Person from '../components/Person.vue'

export default {
	components: {
		Person
	},
	data() {
		return {
			//SWAPI data
			people: [],
			page_id: 0,
			total_people: 0,
			people_per_page: 0,
			limit: 10
		}
	},
	methods: {
		onPageChange(page) {
			//get new page
			SWAPI.getPeople(page, this.handlePeople);

			//remove people
			this.people = [];
		},
		handlePeople(response) {
			//if OK
			if (response.status == 200) {
				//store SWAPI data
				this.people = response.data.people;
				this.page_id = response.data.page_id;
				this.total_people = response.data.total_people;
				this.people_per_page = response.data.people_per_page;
			}
		}
	},
	created() {
		//get first page of people by default
		SWAPI.getPeople(1, this.handlePeople);
	},
}
</script>

<style scoped>
	/* src: https://www.w3schools.com/howto/howto_css_loader.asp */
	.loader {
		border: 10px solid #f3f3f3; /* Light grey */
		border-top: 10px solid #3498db; /* Blue */
		border-radius: 50%;
		width: 64px;
		height: 64px;
		animation: spin 2s linear infinite;
		margin: 30px;
	}
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
</style>