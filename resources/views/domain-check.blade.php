<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica Domini</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="container mt-5">
        <div class="text-center">
            <h1 class="mb-4">Cerca Domini Disponibili Alan Ampo (GoDaddy API)</h1>
        </div>

        <form action="{{ route('check') }}" method="POST" class="mb-4">
            @csrf
            <div class="input-group">
                <input id="input-dominio" type="text" name="domain" class="form-control"
                    placeholder="Inserire dominio (es. pippo.com)" required>
                <button type="submit" class="btn btn-primary">Verifica</button>
            </div>
        </form>

        @if(isset($result))
            <div class="alert {{ $result['available'] ? 'alert-success' : 'alert-danger' }}">
                Il dominio <strong>{{ $result['domain'] }}</strong>
                <strong>{{ $result['available'] ? 'è disponibile' : 'NON È disponibile' }}</strong>.
            </div>
        @elseif(isset($result['error']))
            <div class="alert alert-warning">
                Errore: {{ $result['message'] }}
            </div>
        @endif

        @if(isset($whoisInfo) && !isset($whoisInfo['error']))
            <h3>Informazione da WHOIS:</h3>
            <p>Intestato a: {{ $whoisInfo['registrar'] }}</p>
            <p>Data creazione: {{ $whoisInfo['creationDate'] }}</p>
            <p>Data scadenza: {{ $whoisInfo['expirationDate'] }}</p>
        @elseif(isset($whoisInfo['error']))
            <p>{{ $whoisInfo['error'] }}</p>
        @endif
        @if(isset($dnsRecords))
            <h3>DNS Records:</h3>
            @foreach($dnsRecords as $type => $records)
                <h4>{{ $type }} Records:</h4>
                <ul>
                    @foreach($records as $record)
                        <li>{{ json_encode($record) }}</li>
                    @endforeach
                </ul>
            @endforeach
        @endif

    </div>
</body>

</html>
