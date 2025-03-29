<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Leave Management')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Adding Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-900 p-3 sm:p-4 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <!-- App Title -->
            <a href="{{ auth()->user()->isAdmin() ? route('approvals.index') : route('leave-requests.index') }}" class="text-base sm:text-lg font-bold text-white flex items-center">
                <i class="fas fa-clipboard-list mr-2"></i>
                <span>Leave Management</span>
            </a>
            
            <!-- Mobile Menu Button (visible only on mobile) -->
            <button id="mobile-menu-button" class="md:hidden hover:bg-gray-700 transition-colors duration-200 rounded-full p-2">
                <i class="fas fa-bars text-white text-lg"></i>
            </button>
            
            <!-- Desktop Menu (visible only on desktop) -->
            <ul class="hidden md:flex gap-6 items-center">
                @auth
                    @if (auth()->user()->isAdmin())
                        <li>
                            <a href="{{ route('approvals.index') }}" class="flex items-center hover:text-gray-300 transition-colors">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                <span>Manage Leave Requests</span>
                            </a>
                        </li>
                    @else 
                        <li>
                            <a href="{{ route('leave-requests.index') }}" class="flex items-center hover:text-gray-300 transition-colors">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>Leave Requests</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('profile.edit') }}" class="flex items-center hover:text-gray-300 transition-colors">
                            <i class="fas fa-user mr-2"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="flex items-center">
                            @csrf
                            <button type="submit" class="flex items-center hover:text-gray-300 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>
            
            <!-- Mobile Menu (Hidden by default) -->
            <div id="mobile-menu" class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 hidden flex-col justify-center items-center">
                <button id="close-menu" class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
                <ul class="flex flex-col gap-8 text-center">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <li>
                                <a href="{{ route('approvals.index') }}" class="flex flex-col items-center text-white hover:text-gray-300 transition-colors">
                                    <i class="fas fa-clipboard-check text-3xl mb-2"></i>
                                    <span class="text-base">Manage Leave Requests</span>
                                </a>
                            </li>
                        @else 
                            <li>
                                <a href="{{ route('leave-requests.index') }}" class="flex flex-col items-center text-white hover:text-gray-300 transition-colors">
                                    <i class="fas fa-calendar-alt text-3xl mb-2"></i>
                                    <span class="text-base">Leave Requests</span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center text-white hover:text-gray-300 transition-colors">
                                <i class="fas fa-user text-3xl mb-2"></i>
                                <span class="text-base">Profile</span>
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="flex flex-col items-center">
                                @csrf
                                <button type="submit" class="text-white hover:text-gray-300 transition-colors">
                                    <i class="fas fa-sign-out-alt text-3xl mb-2"></i>
                                    <span class="text-base">Logout</span>
                                </button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-6 sm:mt-10 px-2 sm:px-4">
        @yield('content')
    </div>

    <script>
        // JavaScript for the mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeMenu = document.getElementById('close-menu');
            
            if (mobileMenuButton && mobileMenu && closeMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.classList.add('flex');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                });
                
                closeMenu.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                    document.body.style.overflow = ''; // Re-enable scrolling
                });
            }
        });
    </script>
</body>
</html>