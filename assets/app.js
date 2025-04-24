/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "../vendor/picocss/pico/css/pico.min.css";
import "./styles/app.css";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

/*
 * Theme Toggle
 * This script toggles between light and dark themes using localStorage
 * to remember the user's choice.
 */
document.addEventListener("DOMContentLoaded", () => {
    // JavaScript for theme toggle
    const themeToggle = document.getElementById("theme-toggle");
    const htmlElement = document.documentElement;

    // Helper function to set theme
    const setTheme = (theme) => {
        htmlElement.setAttribute("data-theme", theme);
        localStorage.setItem("theme", theme);
        themeToggle.textContent = theme === "dark" ? "☀️" : "🌙";
    };

    // Initialize theme from localStorage or default to light
    const savedTheme = localStorage.getItem("theme") || "light";
    setTheme(savedTheme);

    // Toggle theme on button click
    themeToggle.addEventListener("click", () => {
        const currentTheme = htmlElement.getAttribute("data-theme");
        const newTheme = currentTheme === "light" ? "dark" : "light";
        setTheme(newTheme);
    });
});
