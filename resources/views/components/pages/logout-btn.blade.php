<form method="POST" action="/logout" class="inline">
    @csrf
    <button type="submit"
        class="inline-block px-5 py-1.5 text-sm border bg-interactible-primary-active rounded-lg text-white hover:border-interactible-primary-active hover:bg-white hover:text-interactible-primary-active transition-all duration-100"">
        Logout
    </button>
</form>
