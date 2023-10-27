<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pokédex - Laravel</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>
<body>
    <div class="container">
        <ul id="pokemonList">
            @foreach ($data as $pokemon)
            <li class="pokemon-type-{{ strtolower($pokemon['typeEnglish']) }}">
                <p>
                    {{ $pokemon['name'] }}
                </p>
                <img src="{{ $pokemon['image'] }}" alt="Pokémon {{ $pokemon['name'] }}" width="125px">
                <span>{{ $pokemon['typeTranslated'] }}</span> <!-- aqui usamos o tipo traduzido -->
                <span>Peso: {{ $pokemon['height'] }}Kgs</span>
            </li>
            @endforeach
        </ul>
        <button id="loadMore">
            <img src="/images/pokeball.png" width="40px" alt="Ícone da Pokebola">
            Carregar Mais
        </button>
        <div id="loading" style="display: none;">
            <img src="/images/loading-animation.gif" style="background-color: transparent !important" alt="Carregando..." width="50px">
        </div>
    </div>
    <button id="btnTop" title="Voltar ao topo">Topo</button>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"> </script>
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
            let offset = {{ count($data) }};
            $('#loadMore').click(function() {
                // Mostrar o loading
                $('#loading').show();
                $('#loadMore').hide();
        
                $.get('/index/' + offset, function(data) {
                    data.forEach(pokemon => {
                        $('#pokemonList').append(`
                        <li class="pokemon-type-${pokemon.typeEnglish.toLowerCase()}">
                                <p>${pokemon.name}</p>
                                <img src="${pokemon.image}" alt="${pokemon.name}">
                                <span>${pokemon.typeTranslated}</span> <!-- aqui usamos o tipo traduzido -->
                                <span>Peso: ${pokemon.height}Kgs</span>
                            </li>
                        `);
                    });
                    offset += 21;
        
                    // Esconder o loading
                    $('#loading').hide();
                    $('#loadMore').show();
                });
            });
        });

        $(document).ready(function(){
            // Quando o usuário rola a página para baixo 20px do topo, mostre o botão
            $(window).scroll(function(){
                if ($(this).scrollTop() > 20) {
                    $('#btnTop').fadeIn();
                } else {
                    $('#btnTop').fadeOut();
                }
            });

            // Quando o usuário clica no botão, role a página para o topo do documento
            $('#btnTop').click(function(){
                $('html, body').animate({scrollTop : 0},800);
                return false;
            });
        });
    </script>
</body>
</html>
