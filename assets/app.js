/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "../vendor/picocss/pico/css/pico.colors.min.css";
import "../vendor/picocss/pico/css/pico.min.css";
import "./styles/app.css";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

const THEME_KEY = "theme";
const LIGHT_THEME = "light";
const DARK_THEME = "dark";
const LIGHT_THEME_ICON = "🌙";
const DARK_THEME_ICON = "☀️";

/*
 * Theme Toggle
 * This script toggles between light and dark themes using localStorage
 * to remember the user's choice.
 */
document.addEventListener("DOMContentLoaded", () => {
    // JavaScript for theme toggle
    const themeToggle = document.getElementById("theme-toggle");
    const htmlElement = document.documentElement;
    
    // by default, the button is disabled, to support graceful degradation
    // if JavaScript is disabled in the browser.
    themeToggle?.removeAttribute('disabled');

    // Helper function to set theme
    const setTheme = (theme) => {
        htmlElement?.setAttribute("data-theme", theme);
        localStorage.setItem(THEME_KEY, theme);
        themeToggle && (themeToggle.textContent = theme === DARK_THEME ? LIGHT_THEME_ICON : DARK_THEME_ICON);
    };

    // Initialize theme from localStorage or default to light
    const savedTheme = localStorage.getItem(THEME_KEY) || LIGHT_THEME;
    setTheme(savedTheme);

    // Toggle theme on button click
    themeToggle?.addEventListener("click", () => {
        const currentTheme = htmlElement?.getAttribute("data-theme");
        const newTheme = currentTheme === LIGHT_THEME ? DARK_THEME : LIGHT_THEME;
        setTheme(newTheme);
    });
});
