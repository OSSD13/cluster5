{{ session('user') }}
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="submit">Logout</button>
</form>
