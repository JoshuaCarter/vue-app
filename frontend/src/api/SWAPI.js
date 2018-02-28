import axios from 'axios';

let api_url = 'http://localhost:8000/SWAPI/';

class SWAPI {
	/**
	 * query lumen API for page of people
	 * @param {int} page 
	 * @param {func(object)} callback handler for API response
	 */
	static getPeople(page, callback) {
		axios.get(api_url + 'people/' + page).then(
			(response) => callback(response)
		);
	}
}

export default SWAPI;