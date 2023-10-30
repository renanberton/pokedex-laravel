<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest; 

class PokemonController extends Controller
{
    public function getAllPokemons($offset = 0) 
    {
        $endpoint = "https://pokeapi.co/api/v2/pokemon?limit=21&offset=". $offset;
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Erro na requisição: ' . curl_error($ch);
        } else {
            $data = json_decode($response, true);
        }
        curl_close($ch);
    
        if($data) {
            $client = new Client();
            $requests = function ($data) {
                foreach ($data['results'] as $pokemon) {
                    yield new GuzzleRequest('GET', $pokemon['url']);
                }
            };
    
            $pokemonDetails = [];
            
            $pool = new Pool($client, $requests($data), [
                'concurrency' => 5,
                'fulfilled' => function ($response, $index) use (&$pokemonDetails) {
                    $pokemonData = json_decode($response->getBody(), true);
                    $pokemonDetails[] = [
                        'name' => $pokemonData['name'],
                        'hp' => $pokemonData['stats'][0]['base_stat'],
                        'image' => $pokemonData['sprites']['other']['dream_world']['front_default'],
                        'typeEnglish' => $pokemonData['types'][0]['type']['name'], // tipo em inglês
                        'typeTranslated' => $this->translateType($pokemonData['types'][0]['type']['name']),
                        'height' => $pokemonData['height']
                    ];
                },
                'rejected' => function ($reason, $index) {
                }
            ]);
    
            $promise = $pool->promise();
            $promise->wait();
    
            $pokemonDetailsOrdered = [];
            foreach ($data['results'] as $pokemon) {
                foreach ($pokemonDetails as $detail) {
                    if ($detail['name'] == $pokemon['name']) {
                        $pokemonDetailsOrdered[] = $detail;
                        break;
                    }
                }
            }
    
            if (request()->ajax()) {
                return response()->json($pokemonDetailsOrdered);
            } else {
                return view('index', ['data' => $pokemonDetailsOrdered]);
            }
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
