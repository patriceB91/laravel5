<footer>
    <p class="text-gray-400">
        &copy; Copywrite {{ date('Y') }}   

        @if(!   Route::is('about')) 
          &middot; <a href="/about" class="text-indigo-500 hover:text-indigo-600 underline"> About us </a>
        @endif

        &nbsp; <a href="/help" class="text-indigo-500 hover:text-indigo-600 underline"> Help </a>
        &nbsp; <a href="/file-upload" class="text-indigo-500 hover:text-indigo-600 underline"> File Upload </a>
    </p>
    <p> Construit avec laravel 8 - By PB Services </p>
</footer>
