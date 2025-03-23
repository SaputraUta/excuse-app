<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Leave Management')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-900 p-4 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold text-white">Dashboard</a>
            <ul class="flex gap-4">
                @auth
                    @if (auth()->user()->isAdmin())
                        <li><a href="{{ route('approvals.index') }}" class="hover:text-gray-300">Manage Leave Requests</a></li>
                    @else 
                        <li><a href="{{ route('leave-requests.index') }}" class="hover:text-gray-300">Leave Requests</a></li>
                    @endif
                    <li><a href="{{ route('profile.edit') }}" class="hover:text-gray-300">Profile</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="hover:text-gray-300">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    <div class="container mx-auto mt-10">
        @yield('content')
    </div>
</body>
</html>
