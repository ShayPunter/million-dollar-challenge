<template>
    <Head>
        <title>Legal - MyMillionDollarGrid.com</title>
        <meta name="description" content="Terms of Service and Privacy Policy for MyMillionDollarGrid.com">
        <meta name="robots" content="noindex,nofollow">
    </Head>

    <div class="min-h-screen bg-gray-100 dark:bg-vulcan-950">
        <div class="fixed bottom-4 right-4 z-50">
            <a
                href="https://mymilliondollargrid.com"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 bg-white dark:bg-gray-800 rounded-full px-3 py-1 shadow-lg transition-all duration-200 hover:shadow-xl"
            >
                <span>Create Your</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
                <span>Million Dollar Grid</span>
            </a>
        </div>

        <!-- Header -->
        <header class="bg-white dark:bg-vulcan-950">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl text-center font-bold text-gray-900 dark:text-gray-100">
                    Legal Documents
                </h1>
                <p class="mt-2 text-center text-gray-600 dark:text-gray-400">
                    Terms of Service & Privacy Policy
                </p>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Terms of Service Section -->
            <div class="bg-white dark:bg-vulcan-900 rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Terms of Service</h2>
                <div class="space-y-6 text-gray-700 dark:text-gray-300">
                    <div v-for="(section, index) in tosContent" :key="`tos-${index}`" class="space-y-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ section.title }}
                        </h3>
                        <p v-if="section.text">{{ section.text }}</p>
                        <ul v-if="section.items" class="list-disc pl-5 space-y-1">
                            <li v-for="(item, itemIndex) in section.items" :key="`tos-item-${itemIndex}`">
                                {{ item }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Privacy Policy Section -->
            <div class="bg-white dark:bg-vulcan-900 rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Privacy Policy</h2>
                <div class="space-y-6 text-gray-700 dark:text-gray-300">
                    <div v-for="(section, index) in privacyContent" :key="`privacy-${index}`" class="space-y-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ section.title }}
                        </h3>
                        <p v-if="section.text">{{ section.text }}</p>
                        <ul v-if="section.items" class="list-disc pl-5 space-y-1">
                            <li v-for="(item, itemIndex) in section.items" :key="`privacy-item-${itemIndex}`">
                                {{ item }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-center text-gray-600 dark:text-gray-400">
                Created with <span class="text-red-500">❤</span> by MyMillionDollarGrid
            </p>
        </main>
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";

const tosContent = [
    {
        title: "1. Acceptance of Terms",
        text: "By accessing or using MyMillionDollarGrid.com (\"the Site\"), you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the Site."
    },
    {
        title: "2. Account Registration and Security",
        items: [
            "You must provide accurate and complete information when creating an account",
            "You are responsible for maintaining the security of your account credentials",
            "You must be at least 13 years of age to use this service",
            "One account per user is permitted"
        ]
    },
    {
        title: "3. Prohibited Activities",
        items: [
            "Posting harmful, malicious, or phishing links",
            "Engaging in spamming activities",
            "Creating multiple accounts",
            "Attempting to manipulate or exploit the system",
            "Harassing other users",
            "Posting content that infringes on intellectual property rights",
            "Using automated scripts or bots without authorization"
        ]
    },
    {
        title: "4. Account Termination",
        text: "We reserve the right to suspend or terminate accounts that:",
        items: [
            "Violate these Terms of Service",
            "Engage in prohibited activities",
            "Provide false information",
            "Remain inactive for extended periods"
        ]
    },
    {
        title: "5. Modifications",
        text: "We reserve the right to modify these terms at any time. Continued use of the Site after changes constitutes acceptance of the modified terms."
    },
    {
        title: "6. Disclaimer",
        text: "The Site is provided \"as is\" without warranties of any kind, either express or implied."
    }
];

const privacyContent = [
    {
        title: "1. Information We Collect",
        text: "We collect the following information:",
        items: [
            "Name",
            "Email address",
            "Authentication information through Google Sign-In"
        ]
    },
    {
        title: "2. How We Use Your Information",
        text: "We use your information solely for:",
        items: [
            "Account creation and management",
            "Communication regarding your account",
            "Site functionality and features",
            "Security and fraud prevention"
        ]
    },
    {
        title: "3. Information Sharing",
        text: "We do not share, sell, rent, or trade your personal information with third parties. The only exception is when required by law or for legal proceedings."
    },
    {
        title: "4. Google Authentication",
        text: "We use Google Sign-In for authentication. When you choose to sign in using Google:",
        items: [
            "We receive basic profile information from your Google account",
            "We do not have access to your Google password",
            "We do not post to your Google account"
        ]
    },
    {
        title: "5. Data Security",
        text: "We implement reasonable security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction."
    },
    {
        title: "6. Your Rights",
        items: [
            "Access your personal information",
            "Request corrections to your information",
            "Request deletion of your account",
            "Opt-out of communications"
        ]
    },
    {
        title: "7. Changes to This Policy",
        text: "We may update this Privacy Policy from time to time. We will notify users of any material changes via email or through the Site."
    }
];
</script>
