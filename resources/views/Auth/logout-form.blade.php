<form method="POST" id="logoutForm" class="visually-hidden ajax-form" action="{{ route('logout') }}" data-confirm="true"
    data-confirm-message="Are you sure you want to logout?">
    @csrf
    <input type="hidden" name="logout">
</form>


