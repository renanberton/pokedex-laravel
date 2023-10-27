<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pokédex - Laravel</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <div class="container">
        <ul id="pokemonList">
            @foreach ($data as $pokemon)
            <li class="pokemon-type-{{ strtolower($pokemon['type']) }}">
                <p>
                    {{ $pokemon['name'] }}
                </p>
                <img src="{{ $pokemon['image'] }}" alt="">
                <span>{{ $pokemon['type'] }}</span>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"> </script>
    <script>
        $(document).ready(function() {
            let offset = {{ count($data) }};
            $('#loadMore').click(function() {
                // Mostrar o loading
                $('#loading').show();
                $('#loadMore').hide();
        
                $.get('/index/' + offset, function(data) {
                    data.forEach(pokemon => {
                        $('#pokemonList').append(`
                            <li class="pokemon-type-${pokemon.type.toLowerCase()}">
                                <p>${pokemon.name}</p>
                                <img src="${pokemon.image}" alt="${pokemon.name}">
                                <span>${pokemon.type}</span>
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
    </script>

</body>
</html>