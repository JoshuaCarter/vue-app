<template>
	<div>
		<b-card>
			<h5 slot="header" class="py-0 my-0">{{data.name}}</h5>
			<div>
				<b-collapse id="none" class="mb-2" v-model="isOpen">
					<table class="w-100">
						<tbody class="w-100">
							<!-- For each person datum -->
							<tr v-for="(val, key) in data" class="w-100">
								<!-- Datum title -->
								<td class="w-50 align-top pr-5">
									<strong>{{formatKey(key)}}</strong>
								</td>
								<!-- Datum value -->
								<td>
									<!-- Handle array -->
									<div v-if="Array.isArray(val)">
										<div v-for="v in val">
											{{v}}
										</div>
									</div>
									<!-- Handle single -->
									<div v-else>
										{{val}}
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</b-collapse>
			</div>
			<!-- Toggle collapse -->
			<b-btn class="p-0" size="sm" variant="link" v-on:click="onIsOpen" block>
				{{isOpen ? "Collapse" : "Expand"}}
			</b-btn>
		</b-card>
	</div>
</template>

<script>
export default {
	props: [
		'data'
	],
	data() {
		return {
			isOpen: false
		}
	},
	methods: {
		onIsOpen() {
			this.isOpen = !this.isOpen;
		},
		formatKey(key) {
			let output = '';
			let last = '';

			//go through chars in name
			for (let i = 0; i < key.length; ++i) {
				let c = key.charAt(i);

				//capitals at start and after spaces/underscore
				if (last === ' ' || last === '' || last === '_') {
					output += c.toUpperCase();
				}
				//replace underscore with space
				else if (c === '_') {
					output += ' ';
				}
				//else leave as is
				else {
					output += c;
				}

				last = c;
			}

			return output;
		}
	}
}
</script>

<style scoped>
	table {
		width: 50%;
	}

	btn {
		text-decoration: none;
	}

	.card-body {
		padding-top: .5em;
		padding-bottom: .5em;
	}

	tr {
		border-bottom: 1px solid #DFDFDF;
	}
</style>