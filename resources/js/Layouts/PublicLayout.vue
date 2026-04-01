<template>
    <div class="min-h-screen flex flex-col">
        <!-- header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="font-bold text-xl text-indigo-600">Company Logo</div>
                <nav class="space-x-4">
                    <a href="#" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">About</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">Contact</a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow max-w-7xl w-full mx-auto py-6 sm:px-6 lg:px-8">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-6">
            <div class="max-w-7xl mx-auto px-4 text-center">
                &copy; {{ new Date().getFullYear() }} BaseAsfy. All rights reserved.
            </div>
        </footer>
    </div>
</template>

<script setup>
// Public layout for customer/guest facing pages
</script>
