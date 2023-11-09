<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if(isset($data))
    <title>Pokédex - {{ ucfirst($data['name']) }}</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>
<body>
    <div class="container" style="padding: 50px 0; height: 100vh;">
        <img src="./images/pokemon-logo.png" class="logo" alt="Pokémon Logo">
        <form action="{{ route('searchPokemon') }}" action="GET">
            <label for="">Pesquise o Pokémon</label>
            <input type="text" id="pokemon" name="pokemon" placeholder="Digite aqui">
            <input type="submit" name="btnSubmit" value="Pesquisar">
        </form>
        <ul id="pokemonList">
            <li class="pokemon-type-{{ strtolower($data['typeEnglish']) }}">
                <div class="box-name">
                    <p>
                        {{ $data['name'] }}
                    </p>
                    <p>
                        HP {{ $data['hp'] }}
                    </p>
                </div>
                <img src="{{ $data['image'] }}" alt="Pokémon {{ $data['name'] }}" width="100px" height="100px">
                <span>{{ $data['typeTranslated'] }}</span>
                <span>Peso: {{ $data['weight'] }}Kgs</span>
            </li>
        </ul>
        @elseif(isset($error))
            <p class="error">{!! $error !!}</p>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"> </script>
    @if(isset($data))
    <script>
        const typeTranslation = {
            "normal": "normal",
            "fighting": "lutador",
            "flying": "voador",
            "poison": "venenoso",
            "ground": "terra",
            "rock": "pedra",
            "bug": "inseto",
            "ghost": "fantasma",
            "steel": "aço",
            "fire": "fogo",
            "water": "água",
            "grass": "grama",
            "electric": "elétrico",
            "psychic": "psíquico",
            "ice": "gelo",
            "dragon": "dragão",
            "dark": "sombrio",
            "fairy": "fada"
        };

        const translateType = (typeInEnglish) => {
            return typeTranslation[typeInEnglish] || typeInEnglish;
        };

        $(document).ready(function() {
            let offset = {{ is_array($data) ? count($data) : 0 }};
            let offset = {{ count($data) }};
            $('#loadMore').click(function() {
                // Mostrar o loading
                $('#loading').show();
                $('#loadMore').hide();
        
                $.get('/index/' + offset, function(data) {
                    data.forEach(pokemon => {
                        $('#pokemonList').append(`
                        <a href="/search-pokemon?pokemon=${pokemon.name}" style="text-decoration: none;">
                            <li class="pokemon-type-${pokemon.typeEnglish.toLowerCase()}">
                                <p>${pokemon.name}
                                    ${ pokemon.hp }
                                </p>
                                <img src="${pokemon.image}" alt="${pokemon.name}" width="100px" height="100px">
                                <span>${pokemon.typeTranslated}</span> <!-- aqui usamos o tipo traduzido -->
                                <span>Peso: ${pokemon.height}Kgs</span>
                            </li>
                        </a>
                        `);
                    });
                    offset += 21;
        
                    // Esconder o loading
                    $('#loading').hide();
                    $('#loadMore').show();
                });
            });
        });
    </script>
    @endif
</body>
</html>
