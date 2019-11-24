@include('sections.head')
<div class="row">
    <form action="{{ action('MainController@index') }}" method="POST">
        {{ csrf_field() }}

        <div class="card-panel hoverable col s6 offset-s3 yellow lighten-3">
            <h5 class="center-align">Get an accurate weather forecast</h5>
            <div class="row">
                <select class="select" id="country" name="country">
                    <option value="" disabled selected>Choose a country</option>
                    <option value="UK">Great Britain</option>
                    <option value="UA">Ukraine</option>
                    <option value="US">USA</option>
                </select>
            </div>

            <div class="row">
                <div class="input-field col s12 m12 l12">
                    <input id="city" name="city" type="text" class="validate" required>
                    <label for="city">Write a name of a city</label>
                    <span class="helper-text" data-error="The field is required"></span>
                </div>
            </div>

            <div class="row right-align">
                <button class="btn waves-effect waves-light" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                </button>
            </div>

        </div>
    </form>
</div>

@if ($isPostMethod)
<div class="row">
    <div class="col s6 offset-s3">
        @if(isset($emptyResponse))
            <p class="red-text">Incorrect parameters: all fields are required!</p>
        @else
            <h5 class="center-align">Weather forecast for {{ $city }}</h5>
            <p>Average temperature: {{ $avg }}</p>
            <p>Successful responses: {{ $total - $errorsCounter }} from {{ $total }}</p>
            <p>Request Execution Time: {{ $time }}</p>
        @endif
    </div>
</div>
@endif

@include('sections.endbody')
