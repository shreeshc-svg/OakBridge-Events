<h2>Hey !</h2> <br><br>


You received an email from : {{ $details['name'] }} <br><br>


User details: <br><br>


Name: {{ $details['name'] }}<br>

Email: {{ $details['email'] }}<br>

Phone: {{ $details['phone'] }}<br>

@if (array_key_exists('message', $details) && $details['message'])
    Message: {{ $details['message'] }}<br><br><br>
@endif


IP: {{ $details['ip'] }}<br>

Link: <a href="{{ $details['url'] }}">{{ $details['url'] }}</a><br><br>



Thanks
