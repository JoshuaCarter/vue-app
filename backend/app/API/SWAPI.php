<?php

namespace App\API;

//needed for guzzle
require '../vendor/autoload.php';

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\json_decode;

class SWAPI
{
	//guzzle client
	private $client = null;
	private $base_uri = 'https://swapi.co/api/';
	private $async_queue = [];

	//default header that we'll use
	private $header = [
		'headers' => [
			'User-Agent' => 'Khada',
			'Accepts' => 'application/json'
		]
	];

	public function __construct()
	{
		//create guzzle client
		$this->client = new Client(['base_uri' => 'https://swapi.co/api/', 'verify' => false]);
	}

	public function getPeople($page)
	{
		//get initial people data
		$query = 'people/?page=' . $page;
		$response = $this->client->get($query, $this->header);

		//get data as assoc
		$data = json_decode($response->getBody(), true);

		//add page id and page count
		$total_people = $data['count'];
		$people_per_page = 10;
		$page_count = ceil($data['count'] / $people_per_page);
		$data = array(
			'page_id' => (int)$page,
			'page_count' => $page_count,
			'total_people' => $total_people,
			'people_per_page' => $people_per_page
		) + $data;

		//remove unneeded props
		unset($data['count']);
		unset($data['next']);
		unset($data['previous']);

		//rename results to people
		$data['people'] = $data['results'];
		unset($data['results']);

		//for each person
		foreach ($data['people'] as &$person) {
			//remove unneeded props
			unset($person['created']);
			unset($person['edited']);
			unset($person['url']);

			//for each person's props
			foreach ($person as $key => &$value) {
				//if is array...
				if (is_array($value)) {
					//...and empty
					if (count($value) === 0) {
						//remove it
						unset($person[$key]);
					}
					//...or urls
					else if (strpos($value[0], 'swapi') !== false) {
						//for each url
						foreach ($value as &$url) {
							//replace this with name from url data
							$this->getAsync($url, $url);
						}
					}
				}
				//or is single url
				else if (strpos($value, 'swapi') !== false) {
					//replace this with name from url data
					$this->getAsync($value, $value);
				}
			}
		}

		//wait for all async items to finish
		foreach ($this->async_queue as $url => $item) {
			$item->wait();
		}

		//echo json_encode($data);
		return json_encode($data);
	}

	private function getAsync($url, &$prop)
	{
		//if already have async item in queue
		if (isset($this->async_queue[$url])) {
			//add prop as target for that item
			$this->async_queue[$url]->addTarget($prop);
		}
		//else add new async item to queue
		else {
			$promise = $this->client->getAsync($url);
			$asyncItem = new AsyncItem($url, $promise, $this->async_queue);
			$asyncItem->addTarget($prop);
			$this->async_queue += array($url => $asyncItem);
		}
	}
}

class AsyncItem
{
	//target url props to change to name from url data
	private $targets = [];
	//promise to wait for
	private $promise = null;
	//url (key) and ref to queue so can remove self when done
	private $url = '';
	private $queue = null;

	public function __construct($url, &$promise, &$queue)
	{
		$this->url = $url;
		$this->queue = &$queue;
		$this->promise = &$promise;
	}

	public function addTarget(&$target)
	{
		//adds target to be updated
		$this->targets[] = &$target;
	}

	public function wait()
	{
		//wait for response and handle it
		$response = $this->promise->wait();
		$this->handle($response);
	}

	private function handle($response)
	{
		//remove this from queue
		unset($this->queue[$this->url]);

		//name from response
		$data = json_decode($response->getBody(), true);
		$name = '';
		if (isset($data['name'])) {
			$name = $data['name'];
		} else if (isset($data['title'])) {
			$name = $data['title'];
		}

		//replace target props with name
		for ($i = 0; $i < count($this->targets); $i++) {
			$this->targets[$i] = $name;
		}
	}
}