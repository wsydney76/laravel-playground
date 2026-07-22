@php
    use App\Models\User;
    use Illuminate\Pagination\LengthAwarePaginator;
    /** @var LengthAwarePaginator<User> $users */
@endphp

<x-layouts::app title="Dashboard - Users">
    <x-layouts::dashboard
        :heading="__('Users')"
        :subheading="__('Manage users')"
    ></x-layouts::dashboard>
</x-layouts::app>
