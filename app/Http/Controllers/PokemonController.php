<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class PokemonController extends Controller
{
    public function searchPokemon(Request $request)
    {
        $pokemon = strtolower($request->input('pokemon'));
        
        if (empty($pokemon) || !$pokemon) {
            return redirect('index');
        }

        $apiUrl = "https://pokeapi.co/api/v2/pokemon/{$pokemon}";
        $caCertPath = storage_path('cacert.pem'); // Caminho para o cacert.pem

        $client = new Client([
            'verify' => false
        ]);
        
        try {
            $response = $client->get($apiUrl);
        
            if ($response->getStatusCode() === 200) {
                $pokemonData = json_decode($response->getBody(), true);
            
                if ($pokemonData) {
                    $pokemonDetails = [
                        'name' => $pokemonData['name'],
                        'hp' => $pokemonData['stats'][0]['base_stat'],
                        'image' => $pokemonData['sprites']['other']['dream_world']['front_default'],
                        'typeEnglish' => $pokemonData['types'][0]['type']['name'],
                        'typeTranslated' => $this->translateType($pokemonData['types'][0]['type']['name']),
                        'weight' => $pokemonData['weight']
                    ];
            
                    if (request()->ajax()) {
                        return response()->json($pokemonDetails);
                    } else {
                        return view('search-pokemon', ['data' => $pokemonDetails]);
                    }
                }
            }
        } catch(\Exception $e) {
            return view('search-pokemon', ['error' => 'O Pokémon não foi encontrado. <br> Verifique o nome e tente novamente.']);
        }
    }

    public function getAllPokemons($offset = 0) 
    {
        $endpoint = "https://pokeapi.co/api/v2/pokemon?limit=21&offset=". $offset;
        $caCertPath = storage_path('cacert.pem'); // Caminho para o cacert.pem

        $client = new Client([
            'verify' => false
        ]);

        $response = $client->get($endpoint);
        $data = json_decode($response->getBody(), true);

        $pokemonDetailsOrdered = [];

        if ($data && isset($data['results'])) {
            $requests = function () use ($data) {
                foreach ($data['results'] as $index => $pokemon) {
                    yield new GuzzleRequest('GET', $pokemon['url'], ['index' => $index]);
                }
            };

            $pool = new Pool($client, $requests(), [
                'concurrency' => 5,
                'fulfilled' => function ($response, $index) use (&$pokemonDetailsOrdered) {
                    $pokemonData = json_decode($response->getBody(), true);
                    $pokemonDetailsOrdered[] = [
                        'index' => $index,
                        'name' => $pokemonData['name'],
                        'hp' => $pokemonData['stats'][0]['base_stat'],
                        'image' => $pokemonData['sprites']['other']['dream_world']['front_default'],
                        'typeEnglish' => $pokemonData['types'][0]['type']['name'],
                        'typeTranslated' => $this->translateType($pokemonData['types'][0]['type']['name']),
                        'height' => $pokemonData['height']
                    ];
                },
                'rejected' => function ($reason, $index) {
                    // Tratar rejeições, se necessário
                }
            ]);

            $promise = $pool->promise();
            $promise->wait();

            // Ordena os Pokémons pelo índice
            usort($pokemonDetailsOrdered, function($a, $b) {
                return $a['index'] - $b['index'];
            });
        }

        if (request()->ajax()) {
            return response()->json($pokemonDetailsOrdered);
        } else {
            return view('index', ['data' => $pokemonDetailsOrdered]);
        }
    }

    private function translateType($typeInEnglish) {
        $typeTranslation = [
            "normal" => "normal",
            "fighting" => "lutador",
            "flying" => "voador",
            "poison" => "venenoso",
            "ground" => "terra",
            "rock" => "pedra",
            "bug" => "inseto",
            "ghost" => "fantasma",
            "steel" => "aço",
            "fire" => "fogo",
            "water" => "água",
            "grass" => "grama",
            "electric" => "elétrico",
            "psychic" => "psíquico",
            "ice" => "gelo",
            "dragon" => "dragão",
            "dark" => "sombrio",
            "fairy" => "fada"
        ];

        return $typeTranslation[$typeInEnglish] ?? $typeInEnglish;
    }
}
